<?php

namespace App\Console\Commands;

use App\Models\Call;
use App\Models\Task;
use App\Service\Callcenter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class SwooleCallcenterRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:callcenter:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '群呼运行';

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
        $redis_key = config('freeswitch.redis_key.callcenter_task');
        Redis::del($redis_key);
        //服务启动或重启时检测是否有已启动的任务
        $res = Task::query()->where(['status'=>2])->select(['id'])->get();
        if ($res->isNotEmpty()) {
            foreach ($res as $key => $task) {
                Redis::rPush($redis_key,$task->id);
            }
        }
        \Swoole\Coroutine\run(function () use ($redis_key) {
            //启动服务
            while (true) {
                $task_id = Redis::lPop($redis_key);
                if ($task_id == null) {
                    sleep(10);
                    continue;
                }
                \Swoole\Coroutine::create(function () use ($task_id){
                    while (true){
                        $task = Task::with(['queue.sips','gateway'])
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
                            Log::info("任务ID：".$task->id."运行日期不满足");
                            continue;
                        }

                        //检测执行时间
                        $now_time = strtotime(date('H:i:s'));
                        if ( $now_time < strtotime($task->time_start) || $now_time > strtotime($task->time_end) ) {
                            //延迟10秒
                            sleep(10);
                            Log::info("任务ID：".$task->id."运行时间不满足");
                            continue;
                        }

                        //检测网关信息
                        if ($task->gateway==null){
                            Log::info("任务ID：".$task->id." 的网关不存在，任务停止");
                            $task->update(['status'=>1]);
                            break;
                        }

                        //检测队列
                        if ($task->queue==null){
                            Log::info("任务ID：".$task->id." 的队列不存在，任务停止");
                            $task->update(['status'=>1]);
                            break;
                        }
                        //检测队列是否有坐席
                        if ($task->queue->sips->isEmpty()){
                            Log::info("任务ID：".$task->id." 的队列无坐席存在，任务停止");
                            $task->update(['status'=>1]);
                            break;
                        }
                        //并发数调节
                        $channel = 0;
                        $members = 0;
                        foreach ($task->queue->sips as $sip){
                            if ($sip->status==1 && $sip->state=='down'){
                                $members++;
                            }
                        }
                        if ($members === 0){
                            Log::info("任务ID：".$task->name." 无空闲坐席，sleep：1秒");
                            sleep(1);
                            continue;
                        }else{
                            if ($task->max_channel==0){
                                $channel = $members;
                            }else{
                                $channel = $task->max_channel > $members ? $members : $task->max_channel;
                            }
                        }

                        //如果通道数还是0，则不需要呼叫
                        if ($channel == 0) {
                            Log::info("任务ID：".$task->name." 的并发不需要呼叫");
                            sleep(10);
                            continue;
                        }

                        //进行呼叫
                        $calls = Call::where('task_id',$task->id)->where('status',1)->orderBy('id','asc')->take($channel)->get();
                        if ($calls->isEmpty()){
                            Log::info("任务：".$task->name."已完成");
                            $task->update(['status'=>3]);
                            break;
                        }
                        foreach ($calls as $call){
                            \Swoole\Coroutine::create(function () use ($call,$task){
                                (new Callcenter($call,$task))->run();
                            });
                            sleep(2);
                        }
                        sleep(6);
                    }
                });
            }
        });
    }
}
