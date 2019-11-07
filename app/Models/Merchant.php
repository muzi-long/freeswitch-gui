<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;

class Merchant extends Authenticatable
{
    use SoftDeletes,Notifiable,HasRoles;

    protected $guard_name = 'merchant';
    protected $table = 'merchant';
    protected $fillable = [
        'uuid',
        'username',
        'password',
        'company_name',
        'contact_name',
        'contact_phone',
        'status',
        'expires_at',
        'sip_num',
        'member_num',
        'queue_num',
        'money',
    ];
    protected $hidden = ['uuid','password'];
    protected $dates = ['expires_at'];
    protected $appends = ['status_name'];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function getStatusNameAttribute()
    {
        return $this->attributes['status_name'] = Arr::get(config('freeswitch.merchant_status'),$this->status);
    }

    /**
     * 可使用的网关
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function gateways()
    {
        return $this->belongsToMany('App\Models\Gateway', 'merchant_gateway','gateway_id','merchant_id')->withPivot(['rate']);
    }

    /**
     * 商户拥有的多个分机
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sips()
    {
        return $this->hasMany('App\Models\Sip','merchant_id','id');
    }

}
