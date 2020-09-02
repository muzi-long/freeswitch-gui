<?php

use Illuminate\Database\Seeder;

class FrontendMenu extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $guard = config('freeswitch.frontend_guard');
        //清空表
        \App\Models\Menu::where('guard_name',$guard)->delete();
        $datas = [
            [
                'name' => '呼叫中心',
                'route' => null,
                'url' => null,
                'icon' => 'layui-icon-auz',
                'type' => 2,
                'sort' => 1,
                'permission_name' => 'frontend.call',
                'child' => [
                    [
                        'name' => '分机管理',
                        'route' => 'frontend.call.sip',
                        'url' => null,
                        'icon' => 'layui-icon-cellphone',
                        'type' => 1,
                        'permission_name' => 'frontend.call.sip',
                    ],
                    [
                        'name' => '我的分机',
                        'route' => 'frontend.call.sip.mine',
                        'url' => null,
                        'icon' => 'layui-icon-heart-fill',
                        'type' => 1,
                        'permission_name' => 'frontend.call.sip.mine',
                    ],
                    [
                        'name' => '通话记录',
                        'route' => 'frontend.call.cdr',
                        'url' => null,
                        'icon' => 'layui-icon-service',
                        'type' => 1,
                        'permission_name' => 'frontend.call.cdr',
                    ],
                ]
            ],
            [
                'name' => '帐号中心',
                'route' => null,
                'url' => null,
                'icon' => 'layui-icon-gift',
                'type' => 2,
                'sort' => 1,
                'permission_name' => 'frontend.account',
                'child' => [
                    [
                        'name' => '商户资料',
                        'route' => 'frontend.account.merchant',
                        'url' => null,
                        'icon' => 'layui-icon-email',
                        'type' => 1,
                        'permission_name' => 'frontend.account.merchant',
                    ],
                    [
                        'name' => '费用明细',
                        'route' => 'frontend.account.bill',
                        'url' => null,
                        'icon' => 'layui-icon-dollar',
                        'type' => 1,
                        'permission_name' => 'frontend.account.bill',
                    ],
                    [
                        'name' => '部门管理',
                        'route' => 'frontend.account.department',
                        'url' => null,
                        'icon' => 'layui-icon-transfer',
                        'type' => 1,
                        'permission_name' => 'frontend.account.department',
                    ],
                    [
                        'name' => '人员管理',
                        'route' => 'frontend.account.staff',
                        'url' => null,
                        'icon' => 'layui-icon-export',
                        'type' => 1,
                        'permission_name' => 'frontend.account.staff',
                    ],
                ]
            ],
            [
                'name' => '系统设置',
                'route' => null,
                'url' => null,
                'icon' => 'layui-icon-set',
                'type' => 2,
                'sort' => 1,
                'permission_name' => 'frontend.system',
                'child' => [
                    [
                        'name' => '个人资料',
                        'route' => 'frontend.system.staff.mine',
                        'url' => null,
                        'icon' => 'layui-icon-water',
                        'type' => 1,
                        'permission_name' => 'frontend.system.staff.mine',
                    ],
                    [
                        'name' => '修改密码',
                        'route' => 'frontend.system.staff.changeMyPasswordForm',
                        'url' => null,
                        'icon' => 'layui-icon-password',
                        'type' => 1,
                        'permission_name' => 0,
                    ],
                    [
                        'name' => '登录日志',
                        'route' => 'frontend.system.staff.loginLog',
                        'url' => null,
                        'icon' => 'layui-icon-form',
                        'type' => 1,
                        'permission_name' => 'frontend.system.staff.loginLog',
                    ],
                ]
            ],
            [
                'name' => '客户管理',
                'route' => null,
                'url' => null,
                'icon' => 'layui-icon-export',
                'type' => 2,
                'sort' => 1,
                'permission_name' => 'frontend.crm',
                'child' => [
                    [
                        'name' => '进度设置',
                        'route' => 'frontend.crm.node',
                        'url' => null,
                        'icon' => 'layui-icon-app',
                        'type' => 1,
                        'permission_name' => 'frontend.crm.node',
                    ],
                    [
                        'name' => '客户设置',
                        'route' => 'frontend.crm.project-design',
                        'url' => null,
                        'icon' => 'layui-icon-note',
                        'type' => 1,
                        'permission_name' => 'frontend.crm.project-design',
                    ],
                    [
                        'name' => '待分配',
                        'route' => 'frontend.crm.assignment',
                        'url' => null,
                        'icon' => 'layui-icon-diamond',
                        'type' => 1,
                        'permission_name' => 'frontend.crm.assignment',
                    ],
                    [
                        'name' => '我的客户',
                        'route' => 'frontend.crm.project',
                        'url' => null,
                        'icon' => 'layui-icon-reply-fill',
                        'type' => 1,
                        'permission_name' => 'frontend.crm.project',
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
