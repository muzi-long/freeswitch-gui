<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('merchant')->truncate();
        //测试商户
        $user = \App\Models\Merchant::create([
            'uuid' => \Faker\Provider\Uuid::uuid(),
            'username' => 'test',
            'password' => bcrypt('123456'),
            'company_name' => '成都顶呱呱',
            'contact_name' => '测试',
            'contact_phone' => '18908221080',
            'status' => 1,
            'expires_at' => '2023-01-01 00:00:00',
            'sip_num' => 99,
            'member_num' => 99,
            'queue_num' => 99,
            'money' => 9999,
        ]);

        //角色
        $role = \App\Models\Role::create([
            'name' => 'tester',
            'display_name' => '测试人员',
            'guard_name' => 'merchant',
        ]);

        /*//权限
        $permissions = [
            [
                'name' => 'merchant-system',
                'display_name' => '帐号管理',
                'route' => '',
                'icon' => 'layui-icon-set',
                'child' => [
                    [
                        'name' => 'merchant-system.user',
                        'display_name' => '会员管理',
                        'route' => 'merchant.member',
                        'child' => [
                            ['name' => 'merchant-system.member.create', 'display_name' => '添加用户','route'=>'merchant.member.create'],
                            ['name' => 'merchant-system.member.edit', 'display_name' => '编辑用户','route'=>'merchant.member.edit'],
                            ['name' => 'merchant-system.member.destroy', 'display_name' => '删除用户','route'=>'merchant.member.destroy'],
                            ['name' => 'merchant-system.member.role', 'display_name' => '分配角色','route'=>'merchant.member.role'],
                        ]
                    ],
                    [
                        'name' => 'merchant-system.role',
                        'display_name' => '角色管理',
                        'route' => 'merchant.role',
                        'child' => [
                            ['name' => 'merchant-system.role.create', 'display_name' => '添加角色','route'=>'merchant.role.create'],
                            ['name' => 'merchant-system.role.edit', 'display_name' => '编辑角色','route'=>'merchant.role.edit'],
                            ['name' => 'merchant-system.role.destroy', 'display_name' => '删除角色','route'=>'merchant.role.destroy'],
                            ['name' => 'merchant-system.role.permission', 'display_name' => '分配权限','route'=>'merchant.role.permission'],
                        ]
                    ],
                    [
                        'name' => 'merchant-system.department',
                        'display_name' => '部门管理',
                        'route' => 'merchant.department',
                        'child' => [
                            ['name' => 'merchant-system.department.create', 'display_name' => '添加','route'=>'merchant.department.create'],
                            ['name' => 'merchant-system.department.edit', 'display_name' => '编辑','route'=>'merchant.department.edit'],
                            ['name' => 'merchant-system.department.destroy', 'display_name' => '删除','route'=>'merchant.department.destroy'],
                        ]
                    ],
                ]
            ],
        ];

        foreach ($permissions as $pem1) {
            //生成一级权限
            $p1 = \App\Models\Permission::create([
                'name' => $pem1['name'],
                'display_name' => $pem1['display_name'],
                'route' => $pem1['route']??'',
                'icon' => $pem1['icon']??1,
                'visiable' => isset($pem1['visiable']) ? $pem1['visiable'] : 1,
                'guard_name' => 'merchant',
            ]);
            //为角色添加权限
            $role->givePermissionTo($p1);
            if (isset($pem1['child'])) {
                foreach ($pem1['child'] as $pem2) {
                    //生成二级权限
                    $p2 = \App\Models\Permission::create([
                        'name' => $pem2['name'],
                        'display_name' => $pem2['display_name'],
                        'parent_id' => $p1->id,
                        'route' => $pem2['route']??1,
                        'icon' => $pem2['icon']??1,
                        'type' => isset($pem2['type']) ? $pem2['type'] : 2,
                        'visiable' => isset($pem2['visiable']) ? $pem2['visiable'] : 1,
                        'guard_name' => 'merchant',
                    ]);
                    //为角色添加权限
                    $role->givePermissionTo($p2);
                    if (isset($pem2['child'])) {
                        foreach ($pem2['child'] as $pem3) {
                            //生成三级权限
                            $p3 = \App\Models\Permission::create([
                                'name' => $pem3['name'],
                                'display_name' => $pem3['display_name'],
                                'parent_id' => $p2->id,
                                'route' => $pem3['route']??'',
                                'icon' => $pem3['icon']??1,
                                'type' => isset($pem2['type']) ? $pem2['type'] : 2,
                                'visiable' => isset($pem1['visiable']) ? $pem1['visiable'] : 2,
                                'guard_name' => 'merchant',
                            ]);
                            //为角色添加权限
                            $role->givePermissionTo($p3);
                        }
                    }

                }
            }
        }*/

        //为用户添加角色
        $user->assignRole($role);


    }
}
