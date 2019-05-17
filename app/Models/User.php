<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable,HasRoles;

    protected $table = 'users';
    protected $appends = ['sip_username'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username','name', 'email', 'password','phone','uuid','sip_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function sip()
    {
        return $this->hasOne('App\Models\Sip','id','sip_id');
    }

    public function getSipUsernameAttribute()
    {
        $sip_username = null;
        if ($this->sip_id){
            $sip_username = $this->sip->username;
        }
        return $this->attributes['sip_username'] = $sip_username;
    }

}
