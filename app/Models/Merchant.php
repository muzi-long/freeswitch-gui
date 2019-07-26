<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Merchant extends Model
{
    use SoftDeletes;

    protected $table = 'merchant';
    protected $fillable = [
        'uuid',
        'username',
        'password',
        'status',
        'company_name',
        'expires_at',
        'sip_num',
        'money',
        'created_user_id',
    ];
    protected $hidden = ['uuid','password'];
    protected $dates = ['expires_at'];
    protected $appends = ['status_name','created_user_name'];

    public function getStatusNameAttribute()
    {
        return $this->attributes['status_name'] = array_get(config('freeswitch.merchant_status'),$this->status);
    }

    public function getCreatedUserNameAttribute()
    {
        return $this->attributes['created_user_name'] = $this->user->name??'未知';
    }

    /**
     * 创建用户
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_user_id', 'id');
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
     * 拥有的分机
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sips()
    {
        return $this->hasMany('App\Models\Sip','merchant_id','id');
    }


}
