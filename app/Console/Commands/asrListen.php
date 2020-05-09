<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class asrListen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asr:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'asr listen';

    protected $channel;
    protected $fs_record = '/usr/local/freeswitch/recordings/';
    protected $record_table = null;

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
        $fs = new \Freeswitchesl();
        $service = config('freeswitch.esl');
        if (!$fs->connect($service['host'], $service['port'], $service['password'])){
            Log::info("asr监听ESL未连接");
            return 0;
        }
        $status = 1;
        $fs->events('plain', 'CHANNEL_ANSWER CHANNEL_HANGUP_COMPLETE');
        while ($status == 1) {

            //录音目录
            $filepath = $this->fs_record . date('Y') . '/' . date('m') . '/' . date('d') . '/';
            $received_parameters = $fs->recvEvent();
            if (!empty($received_parameters)) {
                //记录日志
                $info                   = $fs->serialize($received_parameters, "json");
                $info                   = json_decode($info,true);
                $eventname              = Arr::get($info,"Event-Name"); //事件名称
                $uuid                   = Arr::get($info,"Unique-ID"); //事件名称
                $CallerCallerIDNumber   = Arr::get($info,"Caller-Caller-ID-Number"); //事件名称
                $CallerCalleeIDNumber   = Arr::get($info,"Caller-Destination-Number"); //事件名称

                switch ($eventname){
                    case 'CHANNEL_ANSWER':
                        $otherUuid = Arr::get($info,"Other-Leg-Unique-ID");
                        if ($otherUuid) { //被叫应答后
                            //开启全程录音
                            $fullfile = $filepath . 'full_' . md5($otherUuid . $uuid) . '.wav';
                            if (!file_exists($fullfile)) {
                                $fs->bgapi("uuid_record {$uuid} start {$fullfile} 7200"); //录音
                                $fs->bgapi("uuid_setvar {$uuid} record_file {$fullfile}");//设置录音地址变量
                                $fs->bgapi("uuid_setvar {$otherUuid} record_file {$fullfile}");//设置录音地址变量
                            }
                        }
                        break;
                    case 'CHANNEL_HANGUP_COMPLETE':
                        if (isset($this->channel[$uuid])) {
                            unset($this->channel[$uuid]);
                        }
                        $otherType = Arr::get($info,'Other-Type');
                        $otherUuid = Arr::get($info,'Other-Leg-Unique-ID');
                        $start = Arr::get($info,'variable_start_stamp');
                        $answer = Arr::get($info,'variable_answer_stamp');
                        $end = Arr::get($info,'variable_end_stamp');
                        $extend_content = Arr::get($info,'variable_user_data',null);
                        $extend_content = $extend_content ? decrypt($extend_content) : $extend_content;
                        $record_file = Arr::get($info,'variable_record_file',null);
                        $duration = (int)Arr::get($info,'variable_duration',0);
                        $billsec = (int)Arr::get($info,'variable_billsec',0);
                        $customer_caller = Arr::get($info,'variable_customer_caller',null);
                        if (empty($otherType) || $otherType == 'originatee') {
                            $data = [
                                'table_name' => $this->record_table,
                                'leg_type' => 'A',
                                'uuid' => md5($uuid.$otherUuid),
                                'update_data' => [
                                    'aleg_uuid' => $uuid,
                                    'src' => $CallerCallerIDNumber,
                                    'dst' => $customer_caller?$customer_caller:$CallerCalleeIDNumber,
                                    'aleg_start_at' => $start ? urldecode($start) : null,
                                    'aleg_answer_at' => $answer ? urldecode($answer) : null,
                                    'aleg_end_at' => $end ? urldecode($end) : null,
                                    'duration' => $duration,
                                    'record_file' => $record_file,
                                    'user_data' => $extend_content,
                                ],
                            ];
                        }else{
                            $data = [
                                'table_name' => $this->record_table,
                                'leg_type' => 'B',
                                'unique_id' => md5($otherUuid.$uuid),
                                'update_data' => [
                                    'bleg_uuid' => $uuid,
                                    'bleg_start_at' => $start ? urldecode($start) : null,
                                    'bleg_answer_at' => $answer ? urldecode($answer) : null,
                                    'bleg_end_at' => $end ? urldecode($end) : null,
                                    'billsec' => $billsec,
                                ],
                            ];
                        }
                        Redis::rPush('asr_record_key',json_encode($data));
                        unset($data);
                        break;
                    default:
                        break;
                }
            }
        }
        $fs->disconnect();
    }
    
}
