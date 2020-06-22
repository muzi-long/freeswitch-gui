<?php

use Illuminate\Database\Seeder;

class BackendPermission extends Seeder
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
        \App\Models\Admin::truncate();
        \App\Models\Permission::where('guard_name',$guard)->delete();
        \App\Models\Role::where('guard_name',$guard)->delete();
        //后台用户
        $user = \App\Models\Admin::create([
            'username' => 'root',
            'phone' => '18999999999',
            'nickname' => '超级管理员',
            'password' => bcrypt('123456'),
            'api_token' => hash('sha256', Str::random(60)),
        ]);
        //后台角色
        $role = \App\Models\Role::create([
            'name' => 'root',
            'display_name' => '超级管理员',
            'guard_name' => $guard,
        ]);
        //后台权限
        $permissions = [
            [
                'name' => 'backend.system',
                'display_name' => '系统管理',
                'child' => [
                    [
                        'name' => 'backend.system.admin',
                        'display_name' => '用户管理',
                        'child' => [
                            ['name' => 'backend.system.admin.create', 'display_name' => '添加用户'],
                            ['name' => 'backend.system.admin.edit', 'display_name' => '编辑用户'],
                            ['name' => 'backend.system.admin.resetPassword', 'display_name' => '重置密码'],
                            ['name' => 'backend.system.admin.destroy', 'display_name' => '删除用户'],
                            ['name' => 'backend.system.admin.role', 'display_name' => '分配角色'],
                            ['name' => 'backend.system.admin.permission', 'display_name' => '分配权限'],
                        ]
                    ],
                    [
                        'name' => 'backend.system.role',
                        'display_name' => '角色管理',
                        'child' => [
                            ['name' => 'backend.system.role.create', 'display_name' => '添加角色'],
                            ['name' => 'backend.system.role.edit', 'display_name' => '编辑角色'],
                            ['name' => 'backend.system.role.destroy', 'display_name' => '删除角色'],
                            ['name' => 'backend.system.role.permission', 'display_name' => '分配权限'],
                        ]
                    ],
                    [
                        'name' => 'backend.system.permission',
                        'display_name' => '权限管理',
                        'child' => [
                            ['name' => 'backend.system.permission.create', 'display_name' => '添加权限'],
                            ['name' => 'backend.system.permission.edit', 'display_name' => '编辑权限'],
                            ['name' => 'backend.system.permission.destroy', 'display_name' => '删除权限'],
                        ]
                    ],
                    [
                        'name' => 'backend.system.menu',
                        'display_name' => '菜单管理',
                        'child' => [
                            ['name' => 'backend.system.menu.create', 'display_name' => '添加'],
                            ['name' => 'backend.system.menu.edit', 'display_name' => '编辑'],
                            ['name' => 'backend.system.menu.destroy', 'display_name' => '删除'],
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
            //为用户添加权限
            $user->givePermissionTo($p1);
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
                    $user->givePermissionTo($p2);
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
                            //为用户添加权限
                            $user->givePermissionTo($p3);
                        }
                    }

                }
            }
        }
        //为用户添加角色
        $user->assignRole($role);

    }
}
