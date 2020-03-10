<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Models\Task;
use App\Models\Call;

class callcenterRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'callcenter:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'start swoole server';

    public $server;
    public $client;
    public $machineId = 3;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->client = new client();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

    	$redis_key = config('freeswitch.redis_key.callcenter_task');

    	//服务启动或重启时检测是否有已启动的任务
    	$res = Task::where(['status'=>2])->select(['id'])->get();
    	if ($res->isNotEmpty()) {
    		foreach ($res as $key => $task) {
    			Redis::rPush($redis_key,$task->id);
    		}
    		
    	}

    	//启动服务
        while (true) {
            $task_id = Redis::lPop($redis_key);
            if ($task_id == null) {
                sleep(10);
                continue;
            }
            $process = new \Swoole\Process(function () use ($task_id) {

                $fs = new \Freeswitchesl();
                $service = config('freeswitch.event_socket');
                if ($fs->connect($service['host'], $service['port'], $service['password']) ){
                    //redis自增ID的key,用于生成uuid
                    $callKey = config('freeswitch.redis_key.callcenter_call');
                    while (true){
                        
                        $task = Task::with(['queue','gateway'])
                            ->where('status',2)
                            ->where('id',$task_id)
                            ->first();
                        //检测是否有启动的任务
                        if ($task == null ){
                            break;
                        }

                        //检测执行日期
                        $now_date = strtotime(date('Y-m-d'));
                        if ( $now_date < strtotime($task->date_start) || $now_date > strtotime($task->date_end) ) {
                        	//延迟10秒
                            sleep(10);
                            continue;
                        }

                        //检测执行时间
                        $now_time = strtotime(date('H:i:s'));
                        if ( $now_time < strtotime($task->time_start) || $now_time > strtotime($task->time_end) ) {
                        	//延迟10秒
                            sleep(10);
                            continue;
                        }
                        
                        //检测网关信息
                        if ($task->gateway==null){
                            Log::info("任务ID：".$task->name." 的网关不存在，任务停止");
                            $task->update(['status'=>1]);
                            break;
                        }
                        /*$gw_info = $fs->api("sofia status gateway gw".$task->gateway->id);
                        if (trim($gw_info)=='Invalid Gateway!'){
                            Log::info("任务ID： ".$task->name."的网关 ".$task->gateway->name."的网关配置不存在");
                            continue;
                        }
                        $gw_status = 0;
                        foreach (explode("\n",$gw_info) as $str){
                            if (str_contains($str,"REGED")){
                                $gw_status = 1;
                            }
                        }
                        if ($gw_status==0){
                            Log::info("任务ID： ".$task->name."的网关 ".$task->gateway->name."未注册成功");
                            continue;
                        }*/

                        //检测队列
                        if ($task->queue==null){
                            Log::info("任务ID：".$task->name." 的队列不存在，任务停止");
                            $task->update(['status'=>1]);
                            break;
                        }
                        //并发数调节
                        $channel = 0;
                        if ($task->max_channel){
                            $channel = $task->max_channel;
                        }else{
                            //队列总空闲坐席数
                            $wait_num = $fs->api("callcenter_config queue count agents queue".$task->queue->id." Available Waiting");
                            $channel = (int)$wait_num;
                        }
                        //如果通道数还是0，则不需要呼叫
                        if ($channel == 0) {
                        	sleep(5);
                        	continue;
                        }
                        //否则进行呼叫
                        Log::info("任务：".$task->name." 将呼叫 ".$channel." 个号码");
                        $calls = Call::where('task_id',$task->id)->where('status',1)->orderBy('id','asc')->take($channel)->get();
                        if ($calls->isEmpty()){
                            Log::info("任务：".$task->name."已完成");
                            $task->update(['status'=>3]);
                            break;
                        }
                        foreach ($calls as $call){
                            $uuid = md5(\Snowflake::nextId($this->machineId).$call->phone.Redis::incr($callKey));
                            //更新为正在呼叫
                            $call->update(['status'=>2,'uuid'=>$uuid]);
                            Log::info("更新号码: ".$call->phone." 状态为：2");
                            $phone = $task->gateway->prefix ? $task->gateway->prefix.$call->phone : $call->phone;
                            $varStr  = "{origination_uuid=".$uuid."}";
                            $varStr .= "{effective_caller_id_number=".$call->phone."}";
                            $varStr .= "{effective_caller_id_name=".$call->phone."}";
                            if ($task->gateway->outbound_caller_id){
                                $varStr .= "{origination_caller_id_number=".$task->gateway->outbound_caller_id."}";
                                $varStr .= "{origination_caller_id_name=".$task->gateway->outbound_caller_id."}";
                            }
                            $varStr .= "{cc_export_vars=effective_caller_id_number,effective_caller_id_name}";
                            $dail_string = "originate ".$varStr."sofia/gateway/gw".$task->gateway->id."/".$phone." &callcenter(queue".$task->queue->id.")";
                            Log::info("呼叫：".$dail_string);
                            $fs->bgapi($dail_string);
                            usleep(500);
                        }
                        
                    }
                    $fs->disconnect();
                }

            });
            $process->start();
            
        }

       
    }

    

}
