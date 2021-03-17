<?php
namespace App\Service;

use App\Models\Cdr;
use App\Models\Gateway;
use App\Models\Sip;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EslListen extends SwooleFreeswitch
{
    //通话记录对象
    public $cdr;

    public function __construct($uuid)
    {
        $cdr = Cdr::query()->where('uuid','=',$uuid)->first();
        if ($cdr == null) {
            Log::info(sprintf("通话记录[%s]不存在",$uuid));
            return false;
        }
        if (!$cdr->alge_uuid || !$cdr->bleg_uuid){
            Log::info("未指定呼叫aleg_uuid或bleg_uuid");
            return false;
        }
        $this->cdr = $cdr;
        parent::__construct();
    }

    public function run()
    {

        $record_url = config('freeswitch.record_url');
        $fs_dir = '/usr/local/freeswitch';
        if (!$this->connect()){
            Log::info('esl连接异常');
            return false;
        }

        //是呼叫分机还是呼叫外线电话
        $sip = Sip::query()->where('username',$this->cdr->callee)->first();
        if ($sip == null){
            $gateway = Gateway::query()->where('id','=',$this->cdr->gateway_id)->first();
            if ($gateway == null){
                Log::info(sprintf("呼叫B时网关ID：%d 不存在",$this->cdr->gateway_id));
                return false;
            }
            $outbound = $gateway->outbound_caller_id ? sprintf("{origination_caller_id_number=%s}{origination_caller_id_name=%s}",$gateway->outbound_caller_id,$gateway->outbound_caller_id) : null;
            $originate = sprintf("originate {origination_uuid=%s}user/%s &bridge(%s{origination_uuid=%s}sofia/gateway/gw%d/%s%s)",
                $this->cdr->aleg_uuid,$this->cdr->caller,$outbound,$this->cdr->bleg_uuid,$gateway->id,$gateway->prefix,$this->cdr->caller
            );
        }else{
            $originate = sprintf("originate {origination_uuid=%s}user/%s &bridge({origination_uuid=%s}user/%s)",
                $this->cdr->aleg_uuid,$this->cdr->caller,$this->cdr->bleg_uuid,$this->cdr->callee
            );
        }

        $this->events("CHANNEL_ANSWER CHANNEL_HANGUP_COMPLETE");
        $this->filteruuid($this->cdr->aleg_uuid);
        $this->filteruuid($this->cdr->bleg_uuid);
        $this->bgapi($originate);

        while (true) {
            //录音目录
            $filepath = $fs_dir . '/recordings/' . date('Y/m/d/');
            $received_parameters = $this->recvEvent();
            if (!empty($received_parameters)) {
                $json = $this->serialize($received_parameters);
                $uuid = Arr::get($json, 'Unique-ID', null);
                $eventname = Arr::get($json, 'Event-Name', null);
                $otherUuid = Arr::get($json, 'Other-Leg-Unique-ID', null);
                switch ($eventname) {
                    case 'CHANNEL_ANSWER':
                        //被叫应答后
                        if ($otherUuid) {
                            //开启全程录音
                            $fullfile = $filepath . 'full_' . $this->cdr->uuid . '.wav';
                            $this->bgapi("uuid_record {$uuid} start {$fullfile} 7200");
                            $this->cdr->update([
                                'answer_time' => date('Y-m-d H:i:s'),
                                'record' => str_replace($fs_dir, $record_url, $fullfile),
                            ]);
                        }
                        break;
                    case 'CHANNEL_HANGUP_COMPLETE':
                        if ($this->cdr->end_time == null) {
                            $endTime = date('Y-m-d H:i:s');
                            //A的挂断事件
                            if ($uuid == $this->cdr->aleg_uuid) {
                                $billsec = $this->cdr->answer_time ? strtotime($endTime) - strtotime($this->cdr->answer_time) : 0;
                            } else {
                                $billsec = Arr::get($json, 'variable_billsec', 0);
                            }
                            $this->cdr->update([
                                'end_time' => $endTime,
                                'billsec' => $billsec,
                            ]);
                        }
                        break 2;
                    default:
                        break;
                }
            }
        }
        $this->disconnect();

    }

}
