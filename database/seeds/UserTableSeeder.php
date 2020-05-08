<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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
            'phone' => '18908221080',
            'nickname' => '超级管理员',
            'password' => '123456',
            'api_token' => hash('sha256', Str::random(60)),
        ]);
        //角色
        $role = \App\Models\Role::create([
            'name' => 'root',
            'display_name' => '超级管理员',
        ]);
        //权限
        $permissions = [
            [
                'name' => 'system',
                'display_name' => '系统管理',
                'child' => [
                    [
                        'name' => 'system.user',
                        'display_name' => '用户管理',
                        'child' => [
                            ['name' => 'system.user.create', 'display_name' => '添加用户'],
                            ['name' => 'system.user.edit', 'display_name' => '编辑用户'],
                            ['name' => 'system.user.resetPassword', 'display_name' => '重置密码'],
                            ['name' => 'system.user.destroy', 'display_name' => '删除用户'],
                            ['name' => 'system.user.role', 'display_name' => '分配角色'],
                            ['name' => 'system.user.permission', 'display_name' => '分配权限'],
                        ]
                    ],
                    [
                        'name' => 'system.role',
                        'display_name' => '角色管理',
                        'child' => [
                            ['name' => 'system.role.create', 'display_name' => '添加角色'],
                            ['name' => 'system.role.edit', 'display_name' => '编辑角色'],
                            ['name' => 'system.role.destroy', 'display_name' => '删除角色'],
                            ['name' => 'system.role.permission', 'display_name' => '分配权限'],
                        ]
                    ],
                    [
                        'name' => 'system.permission',
                        'display_name' => '权限管理',
                        'child' => [
                            ['name' => 'system.permission.create', 'display_name' => '添加权限'],
                            ['name' => 'system.permission.edit', 'display_name' => '编辑权限'],
                            ['name' => 'system.permission.destroy', 'display_name' => '删除权限'],
                        ]
                    ],
                    [
                        'name' => 'system.menu',
                        'display_name' => '菜单管理',
                        'child' => [
                            ['name' => 'system.menu.create', 'display_name' => '添加'],
                            ['name' => 'system.menu.edit', 'display_name' => '编辑'],
                            ['name' => 'system.menu.destroy', 'display_name' => '删除'],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'fs',
                'display_name' => '服务配置',
                'child' => [
                    [
                        'name' => 'fs.sip',
                        'display_name' => '分机管理',
                        'child' => [
                            ['name' => 'fs.sip.create', 'display_name' => '添加'],
                            ['name' => 'fs.sip.create_list', 'display_name' => '批量添加'],
                            ['name' => 'fs.sip.edit', 'display_name' => '编辑'],
                            ['name' => 'fs.sip.destroy', 'display_name' => '删除'],
                            ['name' => 'fs.sip.updateXml', 'display_name' => '更新配置'],
                            ['name' => 'fs.sip.updateGateway', 'display_name' => '切换网关'],
                        ]
                    ],
                    [
                        'name' => 'fs.gateway',
                        'display_name' => '网关管理',
                        'child' => [
                            ['name' => 'fs.gateway.create', 'display_name' => '添加'],
                            ['name' => 'fs.gateway.edit', 'display_name' => '编辑'],
                            ['name' => 'fs.gateway.destroy', 'display_name' => '删除'],
                            ['name' => 'fs.gateway.updateXml', 'display_name' => '更新配置'],
                        ]
                    ],
                    [
                        'name' => 'fs.gateway_outbound',
                        'display_name' => '出局号码',
                        'child' => [
                            ['name' => 'fs.gateway_outbound.create', 'display_name' => '添加'],
                            ['name' => 'fs.gateway_outbound.edit', 'display_name' => '编辑'],
                            ['name' => 'fs.gateway_outbound.destroy', 'display_name' => '删除'],
                            ['name' => 'fs.gateway_outbound.import', 'display_name' => '导入'],
                        ]
                    ],
                    [
                        'name' => 'fs.extension',
                        'display_name' => '拨号计划',
                        'child' => [
                            ['name' => 'fs.extension.show', 'display_name' => '详情'],
                            ['name' => 'fs.extension.create', 'display_name' => '添加'],
                            ['name' => 'fs.extension.edit', 'display_name' => '编辑'],
                            ['name' => 'fs.extension.destroy', 'display_name' => '删除'],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'callcenter',
                'display_name' => '群呼管理',
                'child' => [
                    [
                        'name' => 'callcenter.queue',
                        'display_name' => '队列管理',
                        'child' => [
                            ['name' => 'callcenter.queue.create', 'display_name' => '添加'],
                            ['name' => 'callcenter.queue.edit', 'display_name' => '编辑'],
                            ['name' => 'callcenter.queue.destroy', 'display_name' => '删除'],
                            ['name' => 'callcenter.queue.updateXml', 'display_name' => '更新配置'],
                            ['name' => 'callcenter.queue.agent', 'display_name' => '分配坐席'],
                        ]
                    ],
                    [
                        'name' => 'callcenter.agent',
                        'display_name' => '坐席管理',
                        'child' => [
                            ['name' => 'callcenter.agent.create', 'display_name' => '添加'],
                            ['name' => 'callcenter.agent.edit', 'display_name' => '编辑'],
                            ['name' => 'callcenter.agent.destroy', 'display_name' => '删除'],
                            ['name' => 'callcenter.agent.check', 'display_name' => '签入签出'],
                        ]
                    ],
                    [
                        'name' => 'callcenter.task',
                        'display_name' => '任务管理',
                        'child' => [
                            ['name' => 'callcenter.task.create', 'display_name' => '添加'],
                            ['name' => 'callcenter.task.show', 'display_name' => '详情'],
                            ['name' => 'callcenter.task.edit', 'display_name' => '编辑'],
                            ['name' => 'callcenter.task.destroy', 'display_name' => '删除'],
                            ['name' => 'callcenter.task.importCall', 'display_name' => '导入号码'],
                            ['name' => 'callcenter.task.setStatus', 'display_name' => '设置状态'],
                        ]
                    ],

                ]
            ],
            [
                'name' => 'crm',
                'display_name' => '客户管理',
                'child' => [
                    [
                        'name' => 'crm.department',
                        'display_name' => '部门管理',
                        'child' => [
                            ['name' => 'crm.department.create', 'display_name' => '添加'],
                            ['name' => 'crm.department.edit', 'display_name' => '编辑'],
                            ['name' => 'crm.department.destroy', 'display_name' => '删除'],
                        ]
                    ],
                    [
                        'name' => 'crm.node',
                        'display_name' => '节点管理',
                        'child' => [
                            ['name' => 'crm.node.create', 'display_name' => '添加'],
                            ['name' => 'crm.node.edit', 'display_name' => '编辑'],
                            ['name' => 'crm.node.destroy', 'display_name' => '删除'],
                        ]
                    ],
                    [
                        'name' => 'crm.project-design',
                        'display_name' => '客户配置',
                        'child' => [
                            ['name' => 'crm.project-design.create', 'display_name' => '添加'],
                            ['name' => 'crm.project-design.edit', 'display_name' => '编辑'],
                            ['name' => 'crm.project-design.destroy', 'display_name' => '删除'],
                        ]
                    ],
                    [
                        'name' => 'crm.project',
                        'display_name' => '客户管理',
                        'route' => 'admin.project',
                        'child' => [
                            ['name' => 'crm.project.create', 'display_name' => '添加'],
                            ['name' => 'crm.project.edit', 'display_name' => '编辑'],
                            ['name' => 'crm.project.destroy', 'display_name' => '删除'],
                            ['name' => 'crm.project.show', 'display_name' => '详情'],
                            ['name' => 'crm.project.node', 'display_name' => '节点变更'],
                            ['name' => 'crm.project.remark', 'display_name' => '添加备注'],
                            ['name' => 'crm.project.import', 'display_name' => '导入'],
                            ['name' => 'crm.project.downloadTemplate', 'display_name' => '下载模板'],
                            ['name' => 'crm.project.list_all', 'display_name' => '查看所有客户'],
                            ['name' => 'crm.project.list_department', 'display_name' => '查看本部门客户'],
                        ]
                    ],
                    [
                        'name' => 'crm.waste',
                        'display_name' => '公海库',
                        'child' => [
                            ['name' => 'crm.waste.retrieve', 'display_name' => '拾回'],
                        ]
                    ],
                    [
                        'name' => 'crm.remind',
                        'display_name' => '跟进提醒',
                        'child' => [
                            ['name' => 'crm.remind.count', 'display_name' => '图表统计'],
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
