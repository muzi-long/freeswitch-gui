<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //清空表
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \Illuminate\Support\Facades\DB::table('model_has_permissions')->truncate();
        \Illuminate\Support\Facades\DB::table('model_has_roles')->truncate();
        \Illuminate\Support\Facades\DB::table('role_has_permissions')->truncate();
        \Illuminate\Support\Facades\DB::table('users')->truncate();
        \Illuminate\Support\Facades\DB::table('roles')->truncate();
        \Illuminate\Support\Facades\DB::table('permissions')->truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        //用户
        $user = \App\Models\User::create([
            'username' => 'root',
            'phone' => '18908221080',
            'name' => '超级管理员',
            'email' => 'root@dgg.net',
            'password' => bcrypt('123456'),
            'uuid' => \Faker\Provider\Uuid::uuid(),
        ]);

        //角色
        $role = \App\Models\Role::create([
            'name' => 'root',
            'display_name' => '超级管理员'
        ]);

        //权限
        $permissions = [
            [
                'name' => 'system.manage',
                'display_name' => '系统管理',
                'route' => '',
                'icon_id' => '100',
                'child' => [
                    [
                        'name' => 'system.user',
                        'display_name' => '用户管理',
                        'route' => 'admin.user',
                        'icon_id' => '123',
                        'child' => [
                            ['name' => 'system.user.create', 'display_name' => '添加用户','route'=>'admin.user.create'],
                            ['name' => 'system.user.edit', 'display_name' => '编辑用户','route'=>'admin.user.edit'],
                            ['name' => 'system.user.destroy', 'display_name' => '删除用户','route'=>'admin.user.destroy'],
                            ['name' => 'system.user.role', 'display_name' => '分配角色','route'=>'admin.user.role'],
                            ['name' => 'system.user.permission', 'display_name' => '分配权限','route'=>'admin.user.permission'],
                            ['name' => 'system.user.setSip', 'display_name' => '分配外呼号','route'=>'admin.user.setSip'],
                        ]
                    ],
                    [
                        'name' => 'system.role',
                        'display_name' => '角色管理',
                        'route' => 'admin.role',
                        'icon_id' => '121',
                        'child' => [
                            ['name' => 'system.role.create', 'display_name' => '添加角色','route'=>'admin.role.create'],
                            ['name' => 'system.role.edit', 'display_name' => '编辑角色','route'=>'admin.role.edit'],
                            ['name' => 'system.role.destroy', 'display_name' => '删除角色','route'=>'admin.role.destroy'],
                            ['name' => 'system.role.permission', 'display_name' => '分配权限','route'=>'admin.role.permission'],
                        ]
                    ],
                    [
                        'name' => 'system.permission',
                        'display_name' => '权限管理',
                        'route' => 'admin.permission',
                        'icon_id' => '12',
                        'child' => [
                            ['name' => 'system.permission.create', 'display_name' => '添加权限','route'=>'admin.permission.create'],
                            ['name' => 'system.permission.edit', 'display_name' => '编辑权限','route'=>'admin.permission.edit'],
                            ['name' => 'system.permission.destroy', 'display_name' => '删除权限','route'=>'admin.permission.destroy'],
                        ]
                    ],
                    [
                        'name' => 'system.config',
                        'display_name' => '系统配置',
                        'route' => 'admin.config',
                        'icon_id' => '12',
                        'child' => [
                            ['name' => 'system.config.create', 'display_name' => '添加','route'=>'admin.config.create'],
                            ['name' => 'system.config.edit', 'display_name' => '编辑','route'=>'admin.config.edit'],
                            ['name' => 'system.config.destroy', 'display_name' => '删除','route'=>'admin.config.destroy'],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'pbx.manage',
                'display_name' => 'PBX配置',
                'route' => '',
                'icon_id' => '101',
                'child' => [
                    [
                        'name' => 'pbx.group',
                        'display_name' => '分机组',
                        'route' => 'admin.group',
                        'icon_id' => '12',
                        'child' => [
                            ['name' => 'pbx.group.create', 'display_name' => '添加','route'=>'admin.group.create'],
                            ['name' => 'pbx.group.sip', 'display_name' => '分配分机','route'=>'admin.group.sip'],
                            ['name' => 'pbx.group.edit', 'display_name' => '编辑','route'=>'admin.group.edit'],
                            ['name' => 'pbx.group.destroy', 'display_name' => '删除','route'=>'admin.group.destroy'],
                        ]
                    ],
                    [
                        'name' => 'pbx.sip',
                        'display_name' => '分机管理',
                        'route' => 'admin.sip',
                        'icon_id' => '12',
                        'child' => [
                            ['name' => 'pbx.sip.create', 'display_name' => '添加','route'=>'admin.sip.create'],
                            ['name' => 'pbx.sip.create_list', 'display_name' => '批量添加','route'=>'admin.sip.create_list'],
                            ['name' => 'pbx.sip.edit', 'display_name' => '编辑','route'=>'admin.sip.edit'],
                            ['name' => 'pbx.sip.destroy', 'display_name' => '删除','route'=>'admin.sip.destroy'],
                        ]
                    ],
                    [
                        'name' => 'pbx.gateway',
                        'display_name' => '网关管理',
                        'route' => 'admin.gateway',
                        'icon_id' => '12',
                        'child' => [
                            ['name' => 'pbx.gateway.create', 'display_name' => '添加','route'=>'admin.gateway.create'],
                            ['name' => 'pbx.gateway.edit', 'display_name' => '编辑','route'=>'admin.gateway.edit'],
                            ['name' => 'pbx.gateway.destroy', 'display_name' => '删除','route'=>'admin.gateway.destroy'],
                            ['name' => 'pbx.gateway.updateXml', 'display_name' => '更新配置','route'=>'admin.gateway.updateXml'],
                        ]
                    ],
                    [
                        'name' => 'pbx.extension',
                        'display_name' => '拨号计划',
                        'route' => 'admin.extension',
                        'icon_id' => '12',
                        'child' => [
                            ['name' => 'pbx.extension.show', 'display_name' => '详情','route'=>'admin.extension.show'],
                            ['name' => 'pbx.extension.create', 'display_name' => '添加','route'=>'admin.extension.create'],
                            ['name' => 'pbx.extension.edit', 'display_name' => '编辑','route'=>'admin.extension.edit'],
                            ['name' => 'pbx.extension.destroy', 'display_name' => '删除','route'=>'admin.extension.destroy'],
                        ]
                    ],
                    [
                        'name' => 'pbx.queue',
                        'display_name' => '队列管理',
                        'route' => 'admin.queue',
                        'icon_id' => '12',
                        'child' => [
                            ['name' => 'pbx.queue.show', 'display_name' => '详情','route'=>'admin.queue.show'],
                            ['name' => 'pbx.queue.create', 'display_name' => '添加','route'=>'admin.queue.create'],
                            ['name' => 'pbx.queue.edit', 'display_name' => '编辑','route'=>'admin.queue.edit'],
                            ['name' => 'pbx.queue.destroy', 'display_name' => '删除','route'=>'admin.queue.destroy'],
                            ['name' => 'pbx.queue.updateXml', 'display_name' => '更新配置','route'=>'admin.queue.updateXml'],
                            ['name' => 'pbx.queue.agent', 'display_name' => '分配分机','route'=>'admin.queue.agent'],
                        ]
                    ],
                ],
            ],
            [
                'name' => 'record.manage',
                'display_name' => '录音管理',
                'route' => '',
                'icon_id' => '102',
                'child' => [
                    [
                        'name' => 'record.cdr',
                        'display_name' => 'CDR录音',
                        'route' => 'admin.cdr',
                        'icon_id' => '12',
                        'child' => [
                            ['name' => 'pbx.cdr.show', 'display_name' => '通话详单','route'=>'admin.cdr.show'],
                            ['name' => 'pbx.cdr.play', 'display_name' => '播放','route'=>'admin.cdr.play'],
                            ['name' => 'pbx.cdr.download', 'display_name' => '下载','route'=>'admin.cdr.download'],
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
                'icon_id' => $pem1['icon_id']??1,
            ]);
            //为角色添加权限
            $role->givePermissionTo($p1);
            //为用户添加权限
            $user->givePermissionTo($p1);
            if (isset($pem1['child'])) {
                foreach ($pem1['child'] as $pem2) {
                    //生成二级权限
                    $p2 = \App\Models\Permission::create([
                        'name' => $pem2['name'],
                        'display_name' => $pem2['display_name'],
                        'parent_id' => $p1->id,
                        'route' => $pem2['route']??1,
                        'icon_id' => $pem2['icon_id']??1,
                    ]);
                    //为角色添加权限
                    $role->givePermissionTo($p2);
                    //为用户添加权限
                    $user->givePermissionTo($p2);
                    if (isset($pem2['child'])) {
                        foreach ($pem2['child'] as $pem3) {
                            //生成三级权限
                            $p3 = \App\Models\Permission::create([
                                'name' => $pem3['name'],
                                'display_name' => $pem3['display_name'],
                                'parent_id' => $p2->id,
                                'route' => $pem3['route']??'',
                                'icon_id' => $pem3['icon_id']??1,
                            ]);
                            //为角色添加权限
                            $role->givePermissionTo($p3);
                            //为用户添加权限
                            $user->givePermissionTo($p3);
                        }
                    }

                }
            }
        }

        //为用户添加角色
        $user->assignRole($role);

        //初始化的角色
        $roles = [
            ['name' => 'business', 'display_name' => '商务'],
            ['name' => 'assessor', 'display_name' => '审核员'],
            ['name' => 'channel', 'display_name' => '渠道专员'],
            ['name' => 'editor', 'display_name' => '编辑人员'],
            ['name' => 'admin', 'display_name' => '管理员'],
        ];
        foreach ($roles as $role) {
            \App\Models\Role::create($role);
        }

    }
}
