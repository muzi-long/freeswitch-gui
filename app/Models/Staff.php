<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Staff extends Authenticatable
{
    use SoftDeletes,HasRoles;
    protected $table = 'staff';
    protected $fillable = [
        'merchant_id',
        'is_merchant',
        'username',
        'password',
        'nickname',
        'last_login_at',
        'last_login_ip',
        'department_id',
        'sip_id',
    ];

    /**
     * 商户信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function merchant()
    {
        return $this->hasOne(Merchant::class,'id','merchant_id')->withDefault([
            'company_name' => '-',
        ]);
    }

    /**
     * 部门信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function department()
    {
        return $this->hasOne(Department::class,'id','department_id')->withDefault([
            'name' => '-',
        ]);
    }

    /**
     * 分机信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sip()
    {
        return $this->hasOne(Sip::class,'id','sip_id')->withDefault([
            'username' => '-',
        ]);
    }

    public function menus()
    {
        $menus = [];
        $data = Menu::with('childs')
            ->where('parent_id', 0)
            ->where('guard_name', config('freeswitch.frontend_guard'))
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
