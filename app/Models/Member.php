<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;

class Member extends Authenticatable
{
    use Notifiable,HasRoles;
    protected $table = 'member';
    protected $guard_name = 'member';
    protected $fillable = [
        'phone',
        'password',
        'nickname',
        'sip_id',
        'merchant_id',
        'api_token',
        'remember_token',
    ];

    /**
     * 拥有的分机
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sip()
    {
        return $this->hasOne('App\Models\Sip','id','sip_id');
    }

}
