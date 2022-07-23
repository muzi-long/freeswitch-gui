<?php

namespace App\Service;

use App\Models\Call;
use App\Models\Cdr;
use App\Models\Gateway;
use App\Models\Sip;
use App\Models\Task;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class Callcenter
{
    //通话记录对象
    public $call;
    public $task;
    public $fs;

    public function __construct($call, $task)
    {
        $this->call = $call;
        $this->fs = new SwooleFreeswitch();
        if (!$this->fs->connect()) {
            return false;
        }

    }

    public function run()
    {
        $record_url = config('freeswitch.record_url');
        $fs_dir = '/usr/local/freeswitch';

        $uuid = uuid_generate();
        //更新为正在呼叫
        $this->call->update([
            'status' => 2,
            'uuid' => $uuid,
            'datetime_originate_phone' => date('Y-m-d H:i:s')
        ]);
        Log::info("更新号码: " . $this->call->phone . " 状态为：2");

        $phone = $this->task->gateway->prefix ? $this->task->gateway->prefix . $this->call->phone : $this->call->phone;
        $varStr = "{origination_uuid=" . $uuid . "}";
        $varStr .= "{ignore_early_media=true}";
        $varStr .= "{effective_caller_id_number=" . $this->call->phone . "}";
        $varStr .= "{effective_caller_id_name=" . $this->call->phone . "}";
        if ($this->task->gateway->outbound_caller_id) {
            $varStr .= "{origination_caller_id_number=" . $this->task->gateway->outbound_caller_id . "}";
            $varStr .= "{origination_caller_id_name=" . $this->task->gateway->outbound_caller_id . "}";
        }
        $varStr .= "{cc_export_vars=effective_caller_id_number,effective_caller_id_name}";
        $dail_string = "originate " . $varStr . "sofia/gateway/gw" . $this->task->gateway->id . "/" . $phone . " &callcenter(queue" . $this->task->queue->id . ")";
        Log::info("呼叫：" . $dail_string);

        $this->fs->bgapi($dail_string);
        $this->fs->events("CUSTOM callcenter::info");
        $this->fs->filteruuid($this->call->uuid);

        while (true) {
            $received_parameters = $this->fs->recvEvent();
            if (!empty($received_parameters)) {
                $json = $this->fs->serialize($received_parameters);
                $action = Arr::get($json, "CC-Action");
                $uuid = Arr::get($json, "CC-Member-Session-UUID");
                switch ($action) {
                    //呼叫进入队列
                    case 'member-queue-start':
                        $this->call->update([
                            'datetime_entry_queue' => date('Y-m-d H:i:s'),
                            'status' => 3
                        ]);
                        break;
                    // 坐席应答
                    case 'bridge-agent-start':
                        $agent_name = Arr::get($json, "CC-Agent");
                        $id = (int)Str::after($agent_name, 'agent');
                        $filepath = $fs_dir . '/recordings/' . date('Y/m/d/');
                        $file = $filepath . 'callcenter_' . $uuid . '.wav';
                        $this->fs->bgapi("uuid_record " . $uuid . " start " . $file . " 1800");
                        $this->call->update([
                            'datetime_agent_answered' => date('Y-m-d H:i:s'),
                            'status' => 4,
                            'agent_id' => $id,
                            'record_file' => str_replace($fs_dir, $record_url, $file),
                        ]);
                        break;
                    //坐席结束
                    case 'bridge-agent-end':
                        $this->call->update([
                            'datetime_end' => date('Y-m-d H:i:s'),
                            'status' => 4
                        ]);
                        break;
                    //桥接结束，通话结束
                    case 'member-queue-end':
                        $cause = Arr::get($json, "CC-Cause");
                        $answered_time = Arr::get($json, "CC-Agent-Answered-Time");
                        $leaving_time = Arr::get($json, "CC-Member-Leaving-Time");
                        if ($cause == 'Cancel') {
                            $billsec = 0;
                        } else {
                            if ($leaving_time && $answered_time) {
                                $billsec = $leaving_time - $answered_time > 0 ? $leaving_time - $answered_time : 0;
                            } else {
                                $billsec = 0;
                            }
                        }
                        $this->call->update([
                            'billsec' => $billsec,
                        ]);
                        break 2;
                    default:
                }
            }
        }
        $this->fs->disconnect();
    }

}
