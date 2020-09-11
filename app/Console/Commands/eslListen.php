<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class eslListen extends Command
{
    /**
     * 允许事件：'CHANNEL_ANSWER',
                'RECORD_START',
                'RECORD_STOP',
                'CHANNEL_HANGUP_COMPLETE',
     * 多个事件以空格隔开，例：CHANNEL_ANSWER RECORD_START RECORD_STOP CHANNEL_HANGUP_COMPLETE
     * 如果指定uuid则表示只监听指定的uuid的事件
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'esl:listen {event*} {--aleg_uuid=} {--bleg_uuid=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'esl listen events';
    protected $fs_dir = '/usr/local/freeswitch';
    protected $recording_dir = 'recordings';
    public $url = null;
    public $hash_table = 'esl_listen';
    public $cdr_table = 'cdr';
    public $asr_table = 'asr';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        if ($this->url == null){
            $this->url = config('app.url');
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fs = new \Freeswitchesl();
        $service = config('freeswitch.esl');
        if (!$fs->connect($service['host'], $service['port'], $service['password'])){
            Log::error("asr监听ESL未连接");
            return false;
        }
        //======================  接收事件参数验证  ====================
        $eventarr = [
            'CHANNEL_CALLSTATE',
            'CHANNEL_ANSWER',
            'RECORD_START',
            'RECORD_STOP',
            'CHANNEL_HANGUP_COMPLETE',
        ];
        $argument = $this->argument('event');
        foreach ($argument as $name){
            if (!in_array($name,$eventarr)){
                $this->error('event '.$name.' not allowed');
                return false;
            }
        }
        $event = implode(" ",$argument);
        //======================  接收事件参数验证  ====================

        //====================== 是否监听指定的uuid的事件 ===============
        $aleg_uuid = $this->option('aleg_uuid');
        $bleg_uuid = $this->option('bleg_uuid');
        if ($aleg_uuid){
            $fs->filteruuid($aleg_uuid);
        }
        if ($bleg_uuid){
            $fs->filteruuid($bleg_uuid);
        }
        //====================== 是否监听指定的uuid的事件 ===============
        $fs->events('plain', $event);
        while (true) {
            //录音目录
            $filepath = $this->fs_dir . '/' .$this->recording_dir. '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
            $received_parameters = $fs->recvEvent();
            if (!empty($received_parameters)) {
                $this->setTable();
                $info                   = $fs->serialize($received_parameters, "json");
                $info                   = json_decode($info,true);
                $eventname              = Arr::get($info,"Event-Name"); //事件名称
                $uuid                   = Arr::get($info,"Unique-ID"); //UUID
                $CallerCallerIDNumber   = Arr::get($info,"Caller-Caller-ID-Number"); //主叫
                $CallerCalleeIDNumber   = Arr::get($info,"Caller-Destination-Number"); //被叫

                switch ($eventname){
                    //呼叫状态
                    case 'CHANNEL_CALLSTATE':
                        //是分机号才记录
                        if (preg_match('/\d{4,5}/',$CallerCallerIDNumber)){
                            $state = Arr::get($info,'Channel-Call-State');
                            $uniqueid = Arr::get($info,'Caller-Unique-ID');
                            Redis::setex($CallerCallerIDNumber.'_uuid',1200, $uniqueid);
                            DB::table('sip')->where('username',$CallerCallerIDNumber)->update(['state'=>$state]);
                        }
                        break;
                    //通道应答
                    case 'CHANNEL_ANSWER':
                        Redis::hset($this->hash_table,$uuid,json_encode([
                            'pid' => $uuid,
                            'unique_id' => $uuid,
                            'record_file' => null,
                            'full_record_file' => null,
                            'start_time' => date('Y-m-d H:i:s'),
                            'end_time' => null,
                        ]));
                        $otherUuid = Arr::get($info,"Other-Leg-Unique-ID");
                        if ($otherUuid) { //被叫应答后
                            //开启全程录音
                            $fullfile = $filepath . 'full_' . md5($otherUuid . $uuid) . '.wav';
                            $fs->bgapi("uuid_record {$uuid} start {$fullfile} 7200"); //录音

                            //记录A分段录音数据
                            $halffile_a = $filepath . 'half_' . md5($otherUuid . time() . uniqid()) . '.wav';
                            $fs->bgapi("uuid_record " . $otherUuid . " start " . $halffile_a . " 18");
                            $a_data = Redis::hget($this->hash_table,$otherUuid);
                            if ($a_data){
                                $a_data = json_decode(Redis::hget($this->hash_table,$otherUuid),true);
                                $a_data = array_merge($a_data,[
                                    'record_file' => $halffile_a,
                                    'full_record_file' => str_replace($this->fs_dir,$this->url,$fullfile),
                                ]);
                                Redis::hset($this->hash_table,$otherUuid,json_encode($a_data));
                                unset($a_data);
                            }
                            unset($halffile_a);

                            //记录B分段录音数据
                            $halffile_b = $filepath . 'half_' . md5($uuid . time() . uniqid()) . '.wav';
                            $fs->bgapi("uuid_record " . $uuid . " start " . $halffile_b . " 18");
                            $b_data = Redis::hget($this->hash_table,$uuid);
                            if ($b_data){
                                $b_data = json_decode(Redis::hget($this->hash_table,$uuid),true);
                                $b_data = array_merge($b_data,[
                                    'pid' => $otherUuid,
                                    'record_file' => $halffile_b,
                                    'full_record_file' => str_replace($this->fs_dir,$this->url,$fullfile),
                                ]);
                                Redis::hset($this->hash_table,$uuid,json_encode($b_data));
                                unset($b_data);
                            }
                            unset($halffile_b);

                            //更新B接听时间
                            DB::table($this->cdr_table)->where('uuid',$otherUuid)->update([
                                'bleg_answer_at' => date('Y-m-d H:i:s'),
                                'record_file' => str_replace($this->fs_dir,$this->url,$fullfile),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                            unset($fullfile);

                        }else{
                            //更新A接听时间
                            DB::table($this->cdr_table)->where('uuid',$uuid)->update([
                                'aleg_answer_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                        }
                        unset($otherUuid);
                        break;
                    //开始说话
                    case 'RECORD_START':
                        if (Redis::hexists($this->hash_table,$uuid)){
                            $data = json_decode(Redis::hget($this->hash_table,$uuid),true);
                            $data = array_merge($data,[
                                'start_time' => date('Y-m-d H:i:s'),
                            ]);
                            Redis::hset($this->hash_table,$uuid,json_encode($data));
                            unset($data);
                        }
                        break;
                    //结束说话
                    case 'RECORD_STOP':
                        if (Redis::hexists($this->hash_table,$uuid)){
                            $data = json_decode(Redis::hget($this->hash_table,$uuid),true);
                            $data['is_prologue'] += 1;
                            if (isset($data['record_file'])&&file_exists($data['record_file'])){
                                DB::table($this->asr_table)->insert([
                                    'uuid' => $data['pid'],
                                    'leg_uuid' => $data['unique_id'],
                                    'start_at' => $data['start_time'],
                                    'end_at' => date('Y-m-d H:i:s'),
                                    'billsec' => strtotime(date('Y-m-d H:i:s'))-strtotime($data['start_time']),
                                    'record_file' => str_replace($this->fs_dir, $this->url, $data['record_file']),
                                    'created_at' => date('Y-m-d H:i:s'),
                                ]);
                            }

                            //结束说话 后接着开启分段录音
                            $halffile = $filepath . 'half_' . md5($uuid . time() . uniqid()) . '.wav';
                            $fs->bgapi("uuid_record " . $uuid . " start " . $halffile . " 18");
                            $data = array_merge($data,[
                                'record_file' => $halffile,
                                'start_time' => date('Y-m-d H:i:s'),
                                'end_time' => null,
                            ]);
                            Redis::hset($this->hash_table,$uuid,json_encode($data));
                            unset($halffile);
                            unset($data);
                        }
                        break;
                    //挂断
                    case 'CHANNEL_HANGUP_COMPLETE':
                        if (Redis::hexists($this->hash_table,$uuid)){
                            $data = json_decode(Redis::hget($this->hash_table,$uuid),true);
                            Redis::hdel($this->hash_table, $data['pid']);
                            $cdr = DB::table($this->cdr_table)
                                >where('uuid',$data['pid'])
                                ->whereNull('aleg_end_at')
                                ->first();
                            if ($cdr != null){
                                $hanguptime = Arr::get($info,'variable_end_stamp',null);
                                $hanguptime = $hanguptime != null ? urldecode($hanguptime) : null;
                                if ($uuid == $data['pid']){ // A的挂断事件
                                    $callsec = $cdr->bleg_answer_at != null ? strtotime($hanguptime)-strtotime($cdr->bleg_answer_at) : 0;
                                }else{ // B的挂断事件
                                    $callsec = Arr::get($info,'variable_billsec',0);
                                }
                                //更新通话时长
                                DB::table($this->cdr_table)->where('uuid',$data['pid'])->update([
                                    'aleg_end_at' => $hanguptime,
                                    'billsec' => $callsec,
                                ]);
                                unset($callsec);
                                unset($hanguptime);
                            }
                            unset($data);
                            unset($cdr);
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        $fs->disconnect();
    }

    public function setTable(){
        if ($this->cdr_table == null){
            $this->cdr_table = 'cdr_'.date('Ym');
        }
        if ($this->asr_table == null){
            $this->asr_table = 'asr_'.date('Ym');
        }

    }

}
