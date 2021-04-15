<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Gateway extends Model
{
    protected $table = 'gateway';
    protected $fillable = ['name','realm','username','password','prefix','outbound_caller_id','rate','type'];

    /**
     * 查询网关状态
     * @param $gateway
     * @return string
     */
    public static function getStatus($gateway)
    {
        $fs = new \Freeswitchesl();
        try{
            $fs->connect(config('freeswitch.event_socket.host'),config('freeswitch.event_socket.port'),config('freeswitch.event_socket.password'));
        }catch (\Exception $exception){
            Log::info('查询网关状态ESL连接异常：'.$exception->getMessage());
            return 'connect failed';
        }
        $result = $fs->api("sofia status gateway gw".$gateway->id);
        $data = trim($result);
        if ($data=="Invalid Gateway!"){
            return $data;
        }
        foreach (explode("\n",$data) as $item){
            $itemArr = explode("\t",$item);
            if (trim($itemArr[0])=="State"){
                return $itemArr[1];
            }
        }
        $fs->disconnect();
    }
    //出局号码
    public function outbound(){
        return $this->hasMany('App\Models\GatewayOutbound','gateway_id','id');
    }

}
