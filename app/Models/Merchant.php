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
        'contact_name',
        'contact_phone',
        'status',
        'merchant_id',
        'sip_id',
    ];
    protected $hidden = ['uuid','password'];
    protected $dates = ['expires_at'];
    protected $appends = ['status_name'];

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

    /**
     * 绑定的分机
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sip()
    {
        return $this->hasOne('App\Models\Sip','id','sip_id')->withDefault(['username'=>'']);
    }

    /**
     * 商户扩展信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function info()
    {
        return $this->hasOne('App\Models\MerchantInfo','merchant_id','id')->withDefault();
    }

    /**
     * 商户拥有的员工
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function members()
    {
        return $this->hasMany('App\Models\Merchant','merchant_id','id');
    }

    /**
     * 员工所属的商户
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function merchant()
    {
        return $this->hasOne('App\Models\Merchant','id','merchant_id');
    }

}
