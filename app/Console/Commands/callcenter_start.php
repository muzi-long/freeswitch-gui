<?php

namespace App\Console\Commands;

use App\Models\Call;
use App\Models\Task;
use Carbon\Carbon;
use Faker\Provider\Uuid;
use Illuminate\Console\Command;
use Log;

class callcenter_start extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'callcenter:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'callcenter start';

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
        //定义日志
        $monolog = Log::getMonolog();
        $monolog->popHandler();
        Log::useFiles(storage_path('logs/task.log'));
        $fs = new \Freeswitchesl();
        if ($fs->connect(config('freeswitch.event_socket.host'), config('freeswitch.event_socket.port'), config('freeswitch.event_socket.password'))){
            while (true){
                $now_date = date('Y-m-d');
                $now_time = date('H:i:s');
                $tasks = Task::with(['queue','gateway'])
                    ->where('status',2)
                    ->where('date_start','<=',$now_date)
                    ->where('date_end','>=',$now_date)
                    ->where('time_start','<=',$now_time)
                    ->where('time_end','>=',$now_time)
                    ->get();
                //检测是否有启动的任务
                if ($tasks->isEmpty()){
                    //延迟5秒
                    usleep(5000000);
                    continue;
                }
                //循环任务
                foreach ($tasks as $task){
                    //检测网关信息
                    if ($task->gateway==null){
                        Log::info("任务ID：".$task->name." 的网关不存在");
                        continue;
                    }
                    $gw_info = $fs->api("sofia status gateway gw".$task->gateway->id);
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
                    }
                    //检测队列
                    if ($task->queue==null){
                        Log::info("任务ID：".$task->name." 的队列不存在");
                        continue;
                    }
                    //并发数调节
                    $channel = 0;
                    if ($task->max_channel){
                        $channel = $task->max_channel;
                    }else{
                        //队列总空闲坐席数
                        $wait_num = $fs->api("callcenter_config queue count agents ".$task->queue->name." Available Waiting");
                        $channel = (int)$wait_num;
                    }
                    //如果通道数还是0，则不需要呼叫
                    if ($channel==0) continue;
                    //否则进行呼叫
                    Log::info("任务：".$task->name." 将呼叫 ".$channel." 个号码");
                    $calls = Call::where('status',1)->orderBy('id','asc')->limit($channel)->get();
                    if ($calls->isEmpty()){
                        Log::info("任务：".$task->name."已完成");
                        $task->update(['status'=>3]);
                        continue;
                    }
                    foreach ($calls as $call){
                        $uuid = Uuid::uuid();
                        //更新为正在呼叫
                        $call->update(['status'=>2,'uuid'=>$uuid]);
                        Log::info("更新号码: ".$call->phone." 状态为：2");
                        $phone = $task->gateway->prefix ? $task->gateway->prefix.$call->phone : $call->phone;
                        $var_str = "{origination_uuid=".$uuid."}";
                        if ($task->gateway->outbound_caller_id) $var_str = "{origination_uuid=".$uuid.",outbound_caller_id=".$task->gateway->outbound_caller_id."}";
                        $dail_string = "originate ".$var_str."sofia/gateway/gw".$task->gateway->id."/".$phone." &callcenter(".$task->queue->name.")";
                        Log::info("呼叫：".$dail_string);
                        $fs->bgapi($dail_string);
                        usleep(500);
                    }
                }
            }
            return 0;
        }else{
            echo "ESL未连接";
        }
    }
}
