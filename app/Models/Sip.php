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
        'state',
        'status'
    ];

    protected $appends = ['status_name','state_name'];


    public function getStateNameAttribute()
    {
        return $this->attributes['state_name'] = Arr::get(config('freeswitch.channel_callstate'),$this->state,'DOWN');
    }

    public function getStatusNameAttribute()
    {
        return $this->attributes['status'] = Arr::get([0=>'未注册',1=>'已注册'],$this->state,'-');;
    }

    /**
     * 所属网关
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function gateway()
    {
        return $this->hasOne('App\Models\Gateway','id','gateway_id')->withDefault(['name'=>'未分配']);
    }

}
