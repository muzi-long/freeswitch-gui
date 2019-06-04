<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\Call;
use Illuminate\Console\Command;
use Log;

class callcenter_listen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'callcenter:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'callcenter listen';

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
        Log::useFiles(storage_path('logs/listen.log'));

        $fs = new \Freeswitchesl();
        if (!$fs->connect(config('freeswitch.event_socket.host'), config('freeswitch.event_socket.port'), config('freeswitch.event_socket.password'))){
            echo "ESL未连接";
            return 1;
        }
        $fs->events('json', 'CUSTOM callcenter::info');
        while (true){
            $received_parameters = $fs->recvEvent();
            if (!empty($received_parameters)) {
                //记录日志
                $info = $fs->serialize($received_parameters,"json");
                Log::info($info);
                $action = $fs->getHeader($received_parameters,"CC-Action");
                switch ($action){
                    //坐席状态
                    case 'agent-status-change':
                        $agent_name     = $fs->getHeader($received_parameters,"CC-Agent");
                        $status         = $fs->getHeader($received_parameters,"CC-Agent-Status");
                        Agent::where('name',$agent_name)->update(['status'=>$status]);
                        break;
                    //坐席呼叫状态
                    case 'agent-state-change':
                        $agent_name     = $fs->getHeader($received_parameters,"CC-Agent");
                        $state          = $fs->getHeader($received_parameters,"CC-Agent-State");
                        Agent::where('name',$agent_name)->update(['state'=>$state]);
                        break;
                    //呼叫进入队列
                    case 'member-queue-start':
                        $uuid           = $fs->getHeader($received_parameters,"CC-Member-Session-UUID");
                        $datetime       = urldecode($fs->getHeader($received_parameters,"variable_cc_queue_joined_epoch"));
                        Call::where('uuid',$uuid)->update(['datetime_entry_queue'=>date('Y-m-d H:i:s',$datetime),'status'=>3]);
                        break;
                    //呼叫坐席
                    case 'agent-offering':
                        $uuid           = $fs->getHeader($received_parameters,"CC-Member-Session-UUID");
                        $agent_name     = $fs->getHeader($received_parameters,"CC-Agent");
                        $datetime       = urldecode($fs->getHeader($received_parameters,"Event-Date-Local"));
                        Call::where('uuid',$uuid)->update(['datetime_agent_called'=>$datetime,'agent_name'=>$agent_name]);
                        break;
                    // 坐席应答
                    case 'bridge-agent-start':
                        $uuid           = $fs->getHeader($received_parameters,"CC-Member-Session-UUID");
                        $datetime       = $fs->getHeader($received_parameters,"CC-Agent-Answered-Time");
                        Call::where('uuid',$uuid)->update(['datetime_agent_answered'=>date('Y-m-d H:i:s',$datetime),'status'=>4]);
                        break;
                    //坐席结束
                    case 'bridge-agent-end':
                        $uuid           = $fs->getHeader($received_parameters,"CC-Member-Session-UUID");
                        $datetime       = $fs->getHeader($received_parameters,"CC-Bridge-Terminated-Time");
                        Call::where('uuid',$uuid)->update(['datetime_end'=>date('Y-m-d H:i:s',$datetime),'status'=>4]);
                        break;
                    //桥接结束，通话结束
                    case 'member-queue-end':
                        $uuid           = $fs->getHeader($received_parameters,"CC-Member-Session-UUID");
                        $cause          = $fs->getHeader($received_parameters,"CC-Cause");
                        $answered_time  = $fs->getHeader($received_parameters,"CC-Agent-Answered-Time");
                        $leaving_time   = $fs->getHeader($received_parameters,"CC-Member-Leaving-Time");
                        $joined_time    = $fs->getHeader($received_parameters,"CC-Member-Joined-Time");
                        if ($leaving_time && $joined_time){
                            $duration   = $leaving_time - $joined_time > 0 ? $leaving_time - $joined_time : 0;
                        }else{
                            $duration   = 0;
                        }
                        if ($leaving_time && $answered_time){
                            $billsec    = $leaving_time - $answered_time > 0 ? $leaving_time - $answered_time : 0;
                        }else{
                            $billsec    = 0;
                        }
                        Call::where('uuid',$uuid)->update([
                            'cause'                     => $cause,
                            'duration'                  => $duration,
                            'billsec'                   => $billsec,
                        ]);
                        break;
                    default:
                        break;
                }
            }
        }
        $fs->disconnect();
    }
}
