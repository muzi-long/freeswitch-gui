<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sip extends Model
{
    protected $table = 'sip';
    protected $fillable = [
        'username',
        'password',
        'effective_caller_id_name',
        'effective_caller_id_number',
        'outbound_caller_id_name',
        'outbound_caller_id_number',
        'gateway_id',
    ];


    /**
     * 所属网关
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function gateway()
    {
        return $this->hasOne('App\Models\Gateway','id','gateway_id')->withDefault(['name'=>'未分配']);
    }

    /**
     * 查询分机状态
     * @param $sip
     * @return string
     */
    public static function getStatus($sip)
    {
        $fs = new \Freeswitchesl();
        try{
            $fs->connect(config('freeswitch.event_socket.host'),config('freeswitch.event_socket.port'),config('freeswitch.event_socket.password'));
        }catch (\Exception $exception){
            Log::info('查询分机状态ESL连接异常：'.$exception->getMessage());
            return 'connect failed';
        }
        $result = $fs->api("sofia status profile internal reg ".$sip->username);
        $data =  trim($result);
        foreach (explode("\n",$data) as $item){
            $itemArr = explode("\t",$item);
            if (trim($itemArr[0])=="Ping-Status:"){
                return $itemArr[1];
            }
        }
        return "no exisit";
    }

}
