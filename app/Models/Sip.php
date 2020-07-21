<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

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
        'state',
        'status',
        'freeswitch_id',
        'merchant_id',
        'gateway_id',
        'staff_id',
        'last_register_time',
        'last_unregister_time',
    ];
    protected $appends = [
        'status_name',
        'state_name',
    ];
    protected $state_arr = [
        'DOWN'      => '空闲',
        'HANGUP'    => '空闲',
        'RINGING'   => '响铃',
        'RING_WAIT' => '响铃',
        'EARLY'     => '响铃',
        'ACTIVE'    => '通话中',
    ];
    protected $status_arr = [
        0   => '未注册',
        1   => '已注册',
    ];

    public function getStatusNameAttribute()
    {
        return $this->attributes['status_name'] = Arr::get($this->status_arr,$this->status,'-');
    }

    public function getStateNameAttribute()
    {
        return $this->attributes['state_name'] = Arr::get($this->state_arr,$this->state,'-');
    }

    /**
     * 所属FS
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function freeswitch()
    {
        return $this->hasOne(Freeswitch::class,'id','freeswitch_id')->withDefault([
            'name' => '-',
        ]);
    }

    /**
     * 所属商户
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function merchant()
    {
        return $this->hasOne(Merchant::class,'id','merchant_id')->withDefault([
            'company_name' => '-',
        ]);
    }

    /**
     * 绑定网关
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function gateway()
    {
        return $this->hasOne(Gateway::class,'id','gateway_id')->withDefault([
            'name' => '-',
        ]);
    }

}
