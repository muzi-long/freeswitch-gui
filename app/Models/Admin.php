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
        'phone',
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

    public function menus()
    {
        $menus = [];
        $data = Menu::with('childs')
            ->where('parent_id', 0)
            ->where('guard_name', config('freeswitch.backend_guard'))
            ->orderBy('sort','asc')
            ->get();
        foreach ($data as $k1 => $v1){
            if ($v1->permission_id==null || ($v1->permission_id!=null&&$this->hasPermissionTo($v1->permission_id))){
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
                        if ($v2->permission_id==null || ($v2->permission_id!=null&&$this->hasPermissionTo($v2->permission_id))){
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
