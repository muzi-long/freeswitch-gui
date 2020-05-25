<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

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

    protected $appends = ['status','state'];

    public function getStateAttribute()
    {
        $state = Redis::get($this->username.'_state')??0;
        return $this->attributes['state'] = Arr::get(config('freeswitch.channel_callstate'),$state,'-');
    }

    public function getStatusAttribute()
    {
        return $this->attributes['status'] = $this->getStatus($this->username);
    }

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
     * @param $username
     * @return string
     */
    public function getStatus($username)
    {
        $status = null;
        $fs = new \Freeswitchesl();
        $service = config('freeswitch.esl');
        try{
            $fs->connect($service['host'], $service['port'], $service['password']);
            $result = $fs->api("sofia_contact", $username);
            $result = trim($result);
            //只有已注册的连接不用关闭
            if ($result == 'error/user_not_registered') {
                $status = '未注册';
            }else{
                $status = '已注册';
            }
            $fs->disconnect();
        }catch (\Exception $exception){
            Log::info('查询分机状态ESL连接异常：'.$exception->getMessage());
            $status = '连接失败';
        }
        return $status;
    }

}
