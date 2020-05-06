<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable,HasRoles;

    protected $table = 'users';
    protected $guard_name = 'web';

    protected $fillable = [
        'phone',
        'nickname',
        'password',
        'remember_token',
        'api_token',
        'department_id',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function menus()
    {
        $menus = [];
        $data = Menu::with('childs')->where('parent_id', 0)->orderBy('sort','asc')->get();
        foreach ($data as $k1 => $v1){
            if ($this->hasPermissionTo($v1->permission_id)){
                $menus[$k1] = [
                    'name' => $v1->name,
                    'route' => $v1->route,
                    'url' => $v1->url,
                    'icon' => $v1->icon,
                    'type' => $v1->type,
                    'childs' => [],
                ];
                if ($v1->childs->isNotEmpty()){
                    foreach ($v1->childs as $k2 => $v2){
                        if ($this->hasPermissionTo($v2->permission_id)){
                            $menus[$k1]['childs'][$k2] = [
                                'name' => $v2->name,
                                'route' => $v2->route,
                                'url' => $v2->url,
                                'icon' => $v2->icon,
                                'type' => $v2->type,
                            ];
                        }
                    }
                }
            }
        }
        return $menus;
    }

}
