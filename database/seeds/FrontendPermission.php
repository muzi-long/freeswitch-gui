<?php

use Illuminate\Database\Seeder;

class FrontendPermission extends Seeder
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
        \App\Models\Permission::where('guard_name',$guard)->delete();
        \App\Models\Role::where('guard_name',$guard)->delete();
        //后台角色
        $role = \App\Models\Role::create([
            'name' => 'merchant',
            'display_name' => '商户',
            'guard_name' => $guard,
            'merchant_id' => 0,
        ]);
        //后台权限
        $permissions = [
            [
                'name' => 'frontend.call',
                'display_name' => '呼叫中心',
                'child' => [
                    [
                        'name' => 'frontend.call.sip',
                        'display_name' => '分机管理',
                        'child' => [
                            ['name' => 'frontend.call.sip.bind', 'display_name' => '绑定'],
                            ['name' => 'frontend.call.sip.unbind', 'display_name' => '解绑'],
                            ['name' => 'frontend.call.sip.gateway', 'display_name' => '更新网关'],
                        ]
                    ],
                    [
                        'name' => 'frontend.call.sip.mine',
                        'display_name' => '我的分机',
                        'child' => [

                        ]
                    ],
                    [
                        'name' => 'frontend.call.cdr',
                        'display_name' => '通话记录',
                        'child' => [
                            ['name' => 'frontend.call.cdr.merchant', 'display_name' => '查看所有记录'],
                            ['name' => 'frontend.call.cdr.department', 'display_name' => '查看本部门记录'],
                        ]
                    ],
                ]
            ],
        ];

        foreach ($permissions as $pem1) {
            //生成一级权限
            $p1 = \App\Models\Permission::create([
                'guard_name' => $guard,
                'name' => $pem1['name'],
                'display_name' => $pem1['display_name'],
                'parent_id' => 0,
            ]);
            //为角色添加权限
            $role->givePermissionTo($p1);

            if (isset($pem1['child'])) {
                foreach ($pem1['child'] as $pem2) {
                    //生成二级权限
                    $p2 = \App\Models\Permission::create([
                        'guard_name' => $guard,
                        'name' => $pem2['name'],
                        'display_name' => $pem2['display_name'],
                        'parent_id' => $p1->id,
                    ]);
                    //为角色添加权限
                    $role->givePermissionTo($p2);
                    //为用户添加权限
                    if (isset($pem2['child'])) {
                        foreach ($pem2['child'] as $pem3) {
                            //生成三级权限
                            $p3 = \App\Models\Permission::create([
                                'guard_name' => $guard,
                                'name' => $pem3['name'],
                                'display_name' => $pem3['display_name'],
                                'parent_id' => $p2->id,
                            ]);
                            //为角色添加权限
                            $role->givePermissionTo($p3);
                        }
                    }

                }
            }
        }
    }
}
