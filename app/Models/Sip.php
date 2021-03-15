<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class Sip extends Model
{
    protected $table = 'sip';
    protected $guarded = ['id',];

    protected $appends = ['status_name'];

    public function getStatusNameAttribute()
    {
        return $this->attributes['status'] = Arr::get([0=>'未注册',1=>'已注册'],$this->status,'-');;
    }

    /**
     * 所属网关
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function gateway()
    {
        return $this->hasOne('App\Models\Gateway','id','gateway_id')->withDefault(['name'=>'-']);
    }

    /**
     * 所属用户
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\Models\User','sip_id','id')->withDefault(['nickname'=>'-']);
    }

}
