<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class swoole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:cdr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '监听打电话';
    protected $fs_dir = '/usr/local/freeswitch';
    public $url = null;
    public $machineId = 3;
    public $asr_status_key = 'asr_status_key'; //控制是否开启分段录音asr识别的redis key

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->url = config('app.url');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        while (true){
            $data = Redis::lPop(config('freeswitch.fs_dial_key'));
            if ($data == null){
                sleep(2);
                continue;
            }
            $data = json_decode($data,true);
            $process = new \Swoole\Process(function () use ($data) {
                $fs = new \Freeswitchesl();
                $service = config('freeswitch.esl');
                if (!$fs->connect($service['host'], $service['port'], $service['password'])){
                    Log::error("asr监听ESL未连接");
                    return false;
                }
                //监听的事件
                $eventarr = [
                    'CHANNEL_CALLSTATE',
                    'CHANNEL_ANSWER',
                    //'RECORD_START',
                    //'RECORD_STOP',
                    'CHANNEL_HANGUP_COMPLETE',
                ];
                if (isset($data['aleg_uuid'])){
                    $fs->filteruuid($data['aleg_uuid']);
                }
                if (isset($data['bleg_uuid'])){
                    $fs->filteruuid($data['bleg_uuid']);
                }
                if (isset($data['dial_str'])){
                    $fs->bgapi(base64_decode($data['dial_str']));
                }
                $answer_time = 0;
                //录音目录
                $filepath = $this->fs_dir . '/recordings/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
                $fullfile = $filepath . 'full_' . md5($data['aleg_uuid'] . $data['bleg_uuid']) . '.wav';
                $fs->events('plain', implode(" ",$eventarr));
                while (true) {
                    $received_parameters = $fs->recvEvent();
                    if (!empty($received_parameters)) {
                        $serialize              = $fs->serialize($received_parameters,'json');
                        $json                   = json_decode($serialize,true);
                        $eventname              = Arr::get($json,'Event-Name',null); //事件名称
                        $uuid                   = Arr::get($json,'Unique-ID',null);//当前信道leg的uuid
                        $otherUuid              = Arr::get($json,'Other-Leg-Unique-ID',null);
                        $CallerCallerIDNumber   = Arr::get($json,"Caller-Caller-ID-Number"); //主叫
                        $CallerCalleeIDNumber   = Arr::get($json,"Caller-Destination-Number"); //被叫
                        switch ($eventname) {
                            //呼叫状态
                            case 'CHANNEL_CALLSTATE':
                                //是分机号才记录
                                if (preg_match('/\d{4,5}/',$CallerCallerIDNumber)){
                                    $state = Arr::get($json,'Channel-Call-State');
                                    $uniqueid = Arr::get($json,'Caller-Unique-ID');
                                    Redis::setex($CallerCallerIDNumber.'_uuid',1200, $uniqueid);
                                    DB::table('sip')->where('username',$CallerCallerIDNumber)->update(['state'=>$state]);
                                }
                                break;
                            case 'CHANNEL_ANSWER':
                                if ($otherUuid) { //被叫应答后
                                    $answer_time = time();
                                    //开启全程录音
                                    $fs->bgapi("uuid_record {$uuid} start {$fullfile} 7200"); //录音
                                    if (Redis::get($this->asr_status_key)==1) {

                                        //记录A分段录音数据
                                        $halffile_a = $filepath . 'half_' . md5($otherUuid . time() . uniqid()) . '.wav';
                                        $fs->bgapi("uuid_record " . $otherUuid . " start " . $halffile_a . " 18");
                                        Redis::set($otherUuid,json_encode([
                                            'uuid' => $otherUuid,
                                            'leg_uuid' => $otherUuid,
                                            'record_file' => $halffile_a,
                                            'full_record_file' => $fullfile,
                                            'start_at' => date('Y-m-d H:i:s'),
                                            'end_at' => null,
                                        ]));

                                        //记录B分段录音数据
                                        $halffile_b = $filepath . 'half_' . md5($uuid . time() . uniqid()) . '.wav';
                                        $fs->bgapi("uuid_record " . $uuid . " start " . $halffile_b . " 18");
                                        Redis::set($uuid,json_encode([
                                            'uuid' => $otherUuid,
                                            'leg_uuid' => $uuid,
                                            'record_file' => $halffile_b,
                                            'full_record_file' => $fullfile,
                                            'start_at' => date('Y-m-d H:i:s'),
                                            'end_at' => null,
                                        ]));
                                        unset($halffile_a);
                                        unset($halffile_b);
                                    }
                                }
                                break;
                            case 'RECORD_START':
                                $channel = Redis::get($uuid);
                                if ($channel){
                                    $data = array_merge(json_decode($channel,true),[
                                        'start_time' => date('Y-m-d H:i:s'),
                                    ]);
                                    Redis::set($uuid,json_encode($data));
                                }
                                break;
                            case 'RECORD_STOP':
                                if (Redis::get($this->asr_status_key)==1) {
                                    $channel = Redis::get($uuid);
                                    if ($channel){
                                        $data = json_decode($channel,true);
                                        if (isset($data['record_file'])&&file_exists($data['record_file'])){
                                            DB::table('asr')->insert([
                                                'uuid' => $data['uuid'],
                                                'leg_uuid' => $data['leg_uuid'],
                                                'start_at' => $data['start_at'],
                                                'end_at' => date('Y-m-d H:i:s'),
                                                'billsec' => strtotime(date('Y-m-d H:i:s'))-strtotime($data['start_at']),
                                                'record_file' => str_replace($this->fs_dir, $this->url, $data['record_file']),
                                                'created_at' => date('Y-m-d H:i:s'),
                                            ]);
                                        }
                                        //结束说话 后接着开启分段录音
                                        $halffile = $filepath . 'half_' . md5($uuid . time() . uniqid()) . '.wav';
                                        $fs->bgapi("uuid_record " . $uuid . " start " . $halffile . " 18");
                                        Redis::set($uuid,json_encode(array_merge($data,[
                                            'record_file' => $halffile,
                                            'start_at' => date('Y-m-d H:i:s'),
                                            'end_at' => null,
                                        ])));
                                        unset($data);
                                        unset($halffile);
                                    }
                                    unset($channel);
                                }
                                break;
                            case 'CHANNEL_HANGUP_COMPLETE':
                                $otherType = array_get($json,'Other-Type',null);
                                //A的挂机事件到来时线束进程
                                if (empty($otherType) || $otherType == 'originatee') {
                                    $src = array_get($json,'Caller-Caller-ID-Number',null);
                                    $dst = array_get($json,'Caller-Callee-ID-Number',null);
                                    $customer_caller = array_get($json,'variable_customer_caller',null);
                                    $dst = !empty($customer_caller)?$customer_caller:$dst;
                                    $start = array_get($json,'variable_start_stamp',null);
                                    $user_data = array_get($json,'variable_user_data',null);
                                    $record_file = str_replace($this->fs_dir,$this->url,$fullfile);
                                    $billsec = $answer_time!=0?time()-$answer_time:0;
                                    try{
                                        $user_data = decrypt($user_data);
                                    }catch (\Exception $exception){
                                        $user_data = null;
                                    }
                                    try {
                                        $model = DB::table('sip')
                                            ->join('users','sip.id','=','users.sip_id')
                                            ->where('sip.username',$src)
                                            ->select(['users.id','users.department_id'])
                                            ->first();
                                        if ($model == null) break 2;
                                        $cdr = [
                                            'user_id' => $model->id,
                                            'uuid' => $uuid,
                                            'aleg_uuid' => $uuid,
                                            'bleg_uuid' => $uuid,
                                            'direction' => 1,
                                            'src' => $src,
                                            'dst' => $dst,
                                            'duration' => 0,
                                            'billsec' => $billsec,
                                            'record_file' => $record_file,
                                            'user_data' => $user_data,
                                            'created_at' => date('Y-m-d H:i:s'),
                                        ];
                                        DB::table('cdr')->insert($cdr);
                                    }catch (\Exception $exception){
                                        Log::error('写入通话记录异常：'.$exception->getMessage(),$cdr);
                                    }
                                    break 2;
                                }
                            default:
                                # code...
                                break;
                        }
                    }
                }
                $fs->disconnect();
            });
            $process->start();
        }
    }
}
