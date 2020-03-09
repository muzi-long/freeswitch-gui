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
    protected $fs_record = '/usr/local/freeswitch/recordings/';
    public $machineId = 1;

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

            //录音目录
            $filepath = $this->fs_record . date('Y') . '/' . date('m') . '/' . date('d') . '/';
            
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
                            //设置费率
                            /*$sip = Sip::with(['rate', 'merchant'])->where('username', $CallerCallerIDNumber)->first();
                            if ($sip != null && isset($sip->rate->cost1) && isset($sip->merchant->id)) {
                                $fs->bgapi("uuid_setvar {$uuid} nibble_rate {$sip->rate->cost1}");
                                $fs->bgapi("uuid_setvar {$uuid} nibble_account {$sip->merchant->id}");
                                $fs->bgapi("uuid_setvar {$uuid} nibble_increment {$sip->rate->cycle}");
                            }*/
                            //开启全程录音
                            $fullfile = $filepath . 'full_' . md5($otherUuid . $uuid) . '.wav';
                            if (!file_exists($fullfile)) {
                                $fs->bgapi("uuid_record {$uuid} start {$fullfile} 7200"); //录音
                                $fs->bgapi("uuid_setvar {$uuid} record_file {$fullfile}");//设置录音地址变量
                                $fs->bgapi("uuid_setvar {$otherUuid} record_file {$fullfile}");//设置录音地址变量
                            }

                            //开启分段录音A
                            //$halffile_a = $filepath . 'half_' . md5($otherUuid . time() . uniqid()) . '.wav';
                            //$fs->bgapi("uuid_record " . $otherUuid . " start " . $halffile_a . " 18");
                            //记录分段录音数据
                            /*$this->channel[$otherUuid] = [
                                'pid' => $otherUuid,
                                'unique_id' => $otherUuid,
                                'record_file' => $halffile_a,
                                'created_at' => Carbon::now(),
                            ];*/

                            //开启分段录音B
                            //$halffile_b = $filepath . 'half_' . md5($uuid . time() . uniqid()) . '.wav';
                            //$fs->bgapi("uuid_record " . $uuid . " start " . $halffile_b . " 18");
                            //记录分段录音数据
                            /*$this->channel[$uuid] = [
                                'pid' => $otherUuid,
                                'unique_id' => $uuid,
                                'record_file' => $halffile_b,
                                'created_at' => Carbon::now(),
                            ];*/
                        }
                        break;
                    case 'RECORD_STOP':
                            if (isset($this->channel[$uuid])) {
                                /*//发送识别
                                try{
                                    //语音识别 $this->channel[$uuid]['record_file'] 文件
                                    $client = new \Swoole\Client(SWOOLE_SOCK_TCP);
                                    if (!$client->connect('127.0.0.1', 9502, -1)) {
                                        return false;
                                    }
                                    $client->send(json_encode([
                                        'method' => 'asr',
                                        'data' =>[
                                            'url' => $this->url,
                                            'record_file'=>$this->channel[$uuid]['record_file'],
                                            'pid'=>$this->channel[$uuid]['pid'],
                                            'unique_id'=>$this->channel[$uuid]['unique_id'],
                                        ]
                                    ]));
                                    $client->close();
                                }catch(\Exception $e){

                                }*/

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
                        $otherType = $fs->getHeader($received_parameters, "Other-Type");
                        $thoerLegUniqueId = $fs->getHeader($received_parameters, "Other-Leg-Unique-ID");
                        $start = $fs->getHeader($received_parameters, "variable_start_stamp");
                        $answer = $fs->getHeader($received_parameters, "variable_answer_stamp");
                        $end = $fs->getHeader($received_parameters, "variable_end_stamp");
                        $user_data = $fs->getHeader($received_parameters, "variable_user_data");
                        $dgg_caller = $fs->getHeader($received_parameters, "variable_dgg_caller");
                        $cdr = [
                            'caller_id_number' => $CallerCallerIDNumber,
                            'destination_number' => $CallerCalleeIDNumber,
                            'duration' => (int)$fs->getHeader($received_parameters, "variable_duration"),
                            'billsec' => (int)$fs->getHeader($received_parameters, "variable_billsec"),
                            'start_stamp' => $start ? urldecode($start) : null,
                            'answer_stamp' => $answer ? urldecode($answer) : null,
                            'end_stamp' => $end ? urldecode($end) : null,
                            'hangup_cause' => $fs->getHeader($received_parameters, "variable_hangup_cause"),
                            'record_file' => $fs->getHeader($received_parameters, "variable_record_file"),
                        ];
                        try{
                            $cdr['user_data'] = decrypt($user_data);
                        }catch (\Exception $exception){
                            $cdr['user_data'] = null;
                        }

                        if (empty($otherType) || $otherType == 'originatee') {
                            if (!empty($dgg_caller)){
                                $cdr['destination_number'] = $dgg_caller;
                            }
                            $cdr['aleg_uuid'] = $uuid;
                            $cdr['bleg_uuid'] = $thoerLegUniqueId;
                            $table = 'cdr_a_leg';
                        } else {
                            $cdr['aleg_uuid'] = $thoerLegUniqueId;
                            $cdr['bleg_uuid'] = $uuid;
                            $table = 'cdr_b_leg';
                        }
                        DB::table($table)->insert($cdr);
                        //销毁变量
                        unset($cdr);
                        unset($table);
                        unset($otherType);
                        unset($thoerLegUniqueId);
                        unset($start);
                        unset($answer);
                        unset($end);
                        unset($user_data);
                        unset($dgg_caller);
                        break;
                    default:
                        # code...

                }
            }
        }
        $fs->disconnect();
    }

}
