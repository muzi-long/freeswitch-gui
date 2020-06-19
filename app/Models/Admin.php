<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use Notifiable,HasRoles;
    protected $table = 'admin';
    protected $guard_name = 'backend';
    protected $fillable = [
        'nickname',
        'username',
        'password',
        'remember_token',
        'api_token',
        'last_login_at',
        'last_login_ip',
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];

}
