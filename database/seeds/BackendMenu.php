<?php

use Illuminate\Database\Seeder;

class BackendMenu extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $guard = config('freeswitch.backend_guard');
        //清空表
        \App\Models\Menu::where('guard_name',$guard)->delete();
        $datas = [
            [
                'name' => '系统管理',
                'route' => null,
                'url' => null,
                'icon' => 'layui-icon-auz',
                'type' => 2,
                'sort' => 1,
                'permission_name' => 'backend.system',
                'child' => [
                    [
                        'name' => '用户管理',
                        'route' => 'backend.system.admin',
                        'url' => null,
                        'icon' => 'layui-icon-username',
                        'type' => 1,
                        'permission_name' => 'backend.system.admin',
                    ],
                    [
                        'name' => '角色管理',
                        'route' => 'backend.system.role',
                        'url' => null,
                        'icon' => 'layui-icon-group',
                        'type' => 1,
                        'permission_name' => 'backend.system.role',
                    ],
                    [
                        'name' => '权限管理',
                        'route' => 'backend.system.permission',
                        'url' => null,
                        'icon' => 'layui-icon-key',
                        'type' => 1,
                        'permission_name' => 'backend.system.permission',
                    ],
                    [
                        'name' => '菜单管理',
                        'route' => 'backend.system.menu',
                        'url' => null,
                        'icon' => 'layui-icon-menu-fill',
                        'type' => 1,
                        'permission_name' => 'backend.system.menu',
                    ],
                ]
            ],
            [
                'name' => '呼叫中心',
                'route' => null,
                'url' => null,
                'icon' => 'layui-icon-ios',
                'type' => 2,
                'sort' => 2,
                'permission_name' => 'backend.call',
                'child' => [
                    [
                        'name' => 'FS管理',
                        'route' => 'backend.call.freeswitch',
                        'url' => null,
                        'icon' => 'layui-icon-windows',
                        'type' => 1,
                        'permission_name' => 'backend.call.freeswitch',
                    ],
                    [
                        'name' => '拨号计划',
                        'route' => 'backend.call.extension',
                        'url' => null,
                        'icon' => 'layui-icon-chart',
                        'type' => 1,
                        'permission_name' => 'backend.call.extension',
                    ],
                    [
                        'name' => '网关管理',
                        'route' => 'backend.call.gateway',
                        'url' => null,
                        'icon' => 'layui-icon-service',
                        'type' => 1,
                        'permission_name' => 'backend.call.gateway',
                    ],
                    [
                        'name' => '分机管理',
                        'route' => 'backend.call.sip',
                        'url' => null,
                        'icon' => 'layui-icon-cellphone',
                        'type' => 1,
                        'permission_name' => 'backend.call.sip',
                    ],
                    [
                        'name' => '费率管理',
                        'route' => 'backend.call.rate',
                        'url' => null,
                        'icon' => 'layui-icon-senior',
                        'type' => 1,
                        'permission_name' => 'backend.call.rate',
                    ],
                    [
                        'name' => '通话记录',
                        'route' => 'backend.call.cdr',
                        'url' => null,
                        'icon' => 'layui-icon-service',
                        'type' => 1,
                        'permission_name' => 'backend.call.cdr',
                    ],
                ]
            ],
            [
                'name' => '平台管理',
                'route' => null,
                'url' => null,
                'icon' => 'layui-icon-templeate-1',
                'type' => 2,
                'sort' => 2,
                'permission_name' => 'backend.platform',
                'child' => [
                    [
                        'name' => '商户管理',
                        'route' => 'backend.platform.merchant',
                        'url' => null,
                        'icon' => 'layui-icon-user',
                        'type' => 1,
                        'permission_name' => 'backend.platform.merchant',
                    ],
                    [
                        'name' => '员工管理',
                        'route' => 'backend.platform.staff',
                        'url' => null,
                        'icon' => 'layui-icon-username',
                        'type' => 1,
                        'permission_name' => 'backend.platform.staff',
                    ],
                    [
                        'name' => '帐单管理',
                        'route' => 'backend.platform.bill',
                        'url' => null,
                        'icon' => 'layui-icon-dollar',
                        'type' => 1,
                        'permission_name' => 'backend.platform.bill',
                    ],
                ]
            ],
        ];
        $permissions = \App\Models\Permission::where('guard_name',$guard)->pluck('id','name')->toArray();
        foreach ($datas as $k1 => $d1){
            $m1 = \App\Models\Menu::create([
                'name' => $d1['name'],
                'route' => $d1['route'],
                'url' => $d1['url'],
                'icon' => $d1['icon'],
                'type' => $d1['type'],
                'sort' => $k1+1,
                'permission_id' => \Illuminate\Support\Arr::get($permissions,$d1['permission_name'],null),
                'guard_name' => $guard,
            ]);
            if (isset($d1['child'])&&!empty($d1['child'])){
                foreach ($d1['child'] as $k2 => $d2){
                    $m2 = \App\Models\Menu::create([
                        'name' => $d2['name'],
                        'route' => $d2['route'],
                        'url' => $d2['url'],
                        'icon' => $d2['icon'],
                        'type' => $d2['type'],
                        'sort' => $k2+1,
                        'parent_id' => $m1->id,
                        'permission_id' => \Illuminate\Support\Arr::get($permissions,$d2['permission_name'],null),
                        'guard_name' => $guard,
                    ]);
                }
            }
        }

    }
}
