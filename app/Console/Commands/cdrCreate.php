<?php

namespace App\Console\Commands;

use App\Models\Sip;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Redis;

class cdrCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cdr:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'cdr';
    protected $channel;
    protected $fs_dir = '/usr/local/freeswitch';
    protected $machineId = 1;
    protected $url = 'http://localhost';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $service = config('freeswitch.event_socket');
        $fs = new \Freeswitchesl();
        if (!$fs->connect($service['host'], $service['port'], $service['password'])) {
            $this->error("ESL未连接");
            return 1;
        }
        
        $fs->events('plain', 'CHANNEL_ANSWER CHANNEL_HANGUP_COMPLETE');
        while (1) {
            $table = 'cdr_'.date('Ym');
            //录音目录
            $filepath = $this->fs_dir . '/recordings/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
            
            $received_parameters = $fs->recvEvent();
            if (!empty($received_parameters)) {

                $eventname = $fs->getHeader($received_parameters, "Event-Name"); //事件名称
                $uuid = $fs->getHeader($received_parameters, "Unique-ID");//当前信道leg的uuid
                $CallerCallerIDNumber = $fs->getHeader($received_parameters, "Caller-Caller-ID-Number");//打电话者
                $CallerCalleeIDNumber = $fs->getHeader($received_parameters, "Caller-Callee-ID-Number");//接电话者

                switch ($eventname) {
                    case 'CHANNEL_ANSWER':
                        //$this->info($info);
                        $otherUuid = $fs->getHeader($received_parameters, "Other-Leg-Unique-ID");
                        if ($otherUuid) { //被叫应答后
                            DB::table($table)->where('uuid',$otherUuid)->update(['bleg_uuid'=>$uuid]);
                            //开启全程录音
                            $fullfile = $filepath . 'full_' . md5($otherUuid . $uuid) . '.wav';
                            if (!file_exists($fullfile)) {
                                $fs->bgapi("uuid_record {$uuid} start {$fullfile} 7200"); //录音
                                $fs->bgapi("uuid_setvar {$uuid} record_file {$fullfile}");//设置录音地址变量
                                $fs->bgapi("uuid_setvar {$otherUuid} record_file {$fullfile}");//设置录音地址变量
                            }

                            //开启分段录音A
                            $halffile_a = $filepath . 'half_' . md5($otherUuid . time() . uniqid()) . '.wav';
                            $fs->bgapi("uuid_record " . $otherUuid . " start " . $halffile_a . " 18");
                            $this->channel[$otherUuid] = [
                                'uuid' => $otherUuid,
                                'aleg_uuid' => $otherUuid,
                                'bleg_uuid' => $uuid,
                                'record_file' => $halffile_a,
                                'start_at' => date('Y-m-d H:i:s'),
                                'end_at' => null,
                            ];

                            //开启分段录音B
                            $halffile_b = $filepath . 'half_' . md5($uuid . time() . uniqid()) . '.wav';
                            $fs->bgapi("uuid_record " . $uuid . " start " . $halffile_b . " 18");
                            $this->channel[$uuid] = [
                                'uuid' => $otherUuid,
                                'aleg_uuid' => $otherUuid,
                                'bleg_uuid' => $uuid,
                                'record_file' => $halffile_b,
                                'start_at' => date('Y-m-d H:i:s'),
                                'end_at' => null,
                            ];
                        }else{
                            DB::table($table)->insert([
                                'uuid' => $uuid,
                                'aleg_uuid' => $uuid,
                            ]);
                        }
                        break;
                    case 'RECORD_START':
                        if (isset($this->channel[$uuid])) {
                            $this->channel[$uuid]['start_at'] = date('Y-m-d H:i:s');
                        }
                        break;
                    case 'RECORD_STOP':
                        if (isset($this->channel[$uuid])) {
                            $this->channel[$uuid]['end_at'] = date('Y-m-d H:i:s');
                            //结束说话 后接着开启分段录音
                            $halffile = $filepath . 'half_' . md5($uuid . time() . uniqid()) . '.wav';
                            $this->channel[$uuid]['record_file'] = $halffile;
                            $this->channel[$uuid]['created_at'] = Carbon::now();
                            $fs->bgapi("uuid_record " . $uuid . " start " . $halffile . " 18");
                        }
                        break;
                    case 'CHANNEL_HANGUP_COMPLETE':
                        if (isset($this->channel[$uuid])) {
                            unset($this->channel[$uuid]);
                        }
                        //共用变量
                        $otherType = $fs->getHeader($received_parameters, "Other-Type");
                        $start = $fs->getHeader($received_parameters, "variable_start_stamp");
                        $answer = $fs->getHeader($received_parameters, "variable_answer_stamp");
                        $end = $fs->getHeader($received_parameters, "variable_end_stamp");

                        if (empty($otherType) || $otherType == 'originatee') {
                            $dgg_caller = $fs->getHeader($received_parameters, "variable_dgg_caller");
                            $record_file = $fs->getHeader($received_parameters, "variable_record_file");
                            try{
                                $user_data = decrypt($fs->getHeader($received_parameters, "variable_user_data"));
                            }catch (\Exception $exception){
                                $user_data = null;
                            }
                            DB::table($table)->where('uuid',$uuid)
                                ->where('aleg_uuid',$uuid)
                                ->update([
                                    'src' => $CallerCallerIDNumber,
                                    'dst' => !empty($dgg_caller) ? $dgg_caller : $CallerCalleeIDNumber,
                                    'aleg_start_at' => $start ? urldecode($start) : null,
                                    'aleg_answer_at' => $answer ? urldecode($answer) : null,
                                    'aleg_end_at' => $end ? urldecode($end) : null,
                                    'user_data' => $user_data,
                                    'record_file' => !empty($record_file)&&isset($this->url) ? str_replace($this->fs_dir,$this->url,$record_file) : $record_file,
                                    'hangup_cause' => $fs->getHeader($received_parameters, "variable_hangup_cause"),
                                ]);
                            unset($dgg_caller);
                            unset($user_data);
                            unset($record_file);
                        } else {
                            $thoerLegUniqueId = $fs->getHeader($received_parameters, "Other-Leg-Unique-ID");
                            $billsec = $fs->getHeader($received_parameters, "variable_billsec");
                            DB::table($table)->where('uuid',$thoerLegUniqueId)
                                ->where('bleg_uuid',$uuid)
                                ->update([
                                    'bleg_start_at' => $start ? urldecode($start) : null,
                                    'bleg_answer_at' => $answer ? urldecode($answer) : null,
                                    'bleg_end_at' => $end ? urldecode($end) : null,
                                    'billsec' => $billsec,
                                ]);
                            unset($thoerLegUniqueId);
                            unset($billsec);
                        }
                        //销毁公共变量
                        unset($otherType);
                        unset($start);
                        unset($answer);
                        unset($end);
                        break;
                    default:
                        # code...
                }
            }
        }
        $fs->disconnect();
    }

}
