<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\Call;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class callcenterListen extends Command
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
    protected $fs_dir = '/usr/local/freeswitch';

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
            Log::info("群呼监听ESL未连接");
            return 0;
        }
        $fs->events('json', 'CUSTOM callcenter::info');
        while (true){
            $received_parameters = $fs->recvEvent();
            if (!empty($received_parameters)) {
                $action = $fs->getHeader($received_parameters,"CC-Action");
                $uuid = $fs->getHeader($received_parameters,"CC-Member-Session-UUID");
                switch ($action){
                    //坐席状态
                    case 'agent-status-change':
                        $agent_name     = $fs->getHeader($received_parameters,"CC-Agent");
                        $status         = $fs->getHeader($received_parameters,"CC-Agent-Status");
                        $id             = (int)str_after($agent_name,'agent');
                        Agent::where('id',$id)->update(['status'=>urldecode($status)]);
                        break;
                    //坐席呼叫状态
                    case 'agent-state-change':
                        $agent_name     = $fs->getHeader($received_parameters,"CC-Agent");
                        $state          = $fs->getHeader($received_parameters,"CC-Agent-State");
                        $id             = (int)str_after($agent_name,'agent');
                        Agent::where('id',$id)->update(['state'=>urldecode($state)]);
                        break;
                    //呼叫进入队列
                    case 'member-queue-start':
                        $datetime       = urldecode($fs->getHeader($received_parameters,"variable_cc_queue_joined_epoch"));
                        Call::where('uuid',$uuid)->update(['datetime_entry_queue'=>date('Y-m-d H:i:s',$datetime),'status'=>3]);
                        break;
                    // 坐席应答
                    case 'bridge-agent-start':
                        $datetime       = $fs->getHeader($received_parameters,"CC-Agent-Answered-Time");
                        $agent_name     = $fs->getHeader($received_parameters,"CC-Agent");
                        $id             = (int)str_after($agent_name,'agent');
                        //录音
                        $filepath       = $this->fs_dir . '/recordings/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
                        $callcenter_file = $filepath . 'callcenter_' . md5($uuid . time() . uniqid()) . '.wav';
                        $fs->bgapi("uuid_setvar {$uuid} callcenter_record_file {$callcenter_file}");
                        $fs->bgapi("uuid_record " . $uuid . " start " . $callcenter_file . " 1800");
                        Call::where('uuid',$uuid)->update(['datetime_agent_answered'=>date('Y-m-d H:i:s',$datetime),'status'=>4,'agent_id'=>$id]);
                        break;
                    //坐席结束
                    case 'bridge-agent-end':
                        $datetime       = $fs->getHeader($received_parameters,"CC-Bridge-Terminated-Time");
                        $agent_name     = $fs->getHeader($received_parameters,"CC-Agent");
                        $id             = (int)str_after($agent_name,'agent');
                        Call::where('uuid',$uuid)->update(['datetime_end'=>date('Y-m-d H:i:s',$datetime),'status'=>4,'agent_id'=>$id]);
                        break;
                    //桥接结束，通话结束
                    case 'member-queue-end':
                        $cause          = $fs->getHeader($received_parameters,"CC-Cause");
                        $answered_time  = $fs->getHeader($received_parameters,"CC-Agent-Answered-Time");
                        $leaving_time   = $fs->getHeader($received_parameters,"CC-Member-Leaving-Time");
                        $joined_time    = $fs->getHeader($received_parameters,"CC-Member-Joined-Time");
                        //If we get a hangup from the caller before talking to an agent
                        if ($cause == 'Cancel') {
                            $billsec = 0;
                        }else{

                            if ($leaving_time && $answered_time){
                                $billsec = $leaving_time - $answered_time > 0 ? $leaving_time - $answered_time : 0;
                            }else{
                                $billsec = 0;
                            }
                        }
                        if ($billsec > 0) {
                            $record_file = $fs->getHeader($received_parameters,"variable_callcenter_record_file");
                            $data['record_file'] = $record_file ? str_replace('/usr/local/freeswitch',config('app.url'),$record_file) : null;
                        }
                        $data['cause'] = $cause;
                        $data['billsec'] = $billsec;
                        Call::where('uuid',$uuid)->update($data);
                        unset($data);
                        break;
                    default:
                }
            }
        }
        $fs->disconnect();
    }
}