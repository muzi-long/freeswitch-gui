<?php

use Illuminate\Database\Seeder;

class HomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = \App\Models\Role::create([
            'name' => 'home_admin',
            'display_name' => '管理员',
            'guard_name' => 'merchant',
        ]);

        $permissions = [
            [
                'name' => 'home.system.manage',
                'display_name' => '系统管理',
                'route' => '',
                'icon' => 'layui-icon-set',
                'child' => [
                    [
                        'name' => 'home.member',
                        'display_name' => '员工管理',
                        'route' => 'home.member',
                        'child' => [
                            ['name' => 'home.member.create', 'display_name' => '添加','route'=>'home.member.create'],
                            ['name' => 'home.member.edit', 'display_name' => '编辑','route'=>'home.member.edit'],
                            ['name' => 'home.member.destroy', 'display_name' => '删除','route'=>'home.member.destroy'],
                            ['name' => 'home.member.assignSip', 'display_name' => '分配分机','route'=>'home.member.assignSip'],
                            ['name' => 'home.member.assignRole', 'display_name' => '分配角色','route'=>'home.member.assignRole'],
                        ]
                    ],
                    [
                        'name' => 'home.sip',
                        'display_name' => '分机管理',
                        'route' => 'home.sip',
                        'child' => [
                            ['name' => 'home.sip.create', 'display_name' => '添加','route'=>'home.sip.create'],
                            ['name' => 'home.sip.edit', 'display_name' => '编辑','route'=>'home.sip.edit'],
                            ['name' => 'home.sip.destroy', 'display_name' => '删除','route'=>'home.sip.destroy'],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'home.crm.manage',
                'display_name' => 'CRM管理',
                'route' => '',
                'icon' => 'layui-icon-group',
                'child' => [
                    [
                        'name' => 'home.node',
                        'display_name' => '节点管理',
                        'route' => 'home.node',
                        'child' => [
                            ['name' => 'home.node.create', 'display_name' => '添加','route'=>'home.node.create'],
                            ['name' => 'home.node.edit', 'display_name' => '编辑','route'=>'home.node.edit'],
                            ['name' => 'home.node.destroy', 'display_name' => '删除','route'=>'home.node.destroy'],
                        ]
                    ],
                    [
                        'name' => 'home.project-design',
                        'display_name' => '表单设计',
                        'route' => 'home.project-design',
                        'child' => [
                            ['name' => 'home.project-design.create', 'display_name' => '添加','route'=>'home.project-design.create'],
                            ['name' => 'home.project-design.edit', 'display_name' => '编辑','route'=>'home.project-design.edit'],
                            ['name' => 'home.project-design.destroy', 'display_name' => '删除','route'=>'home.project-design.destroy'],
                        ]
                    ],
                    [
                        'name' => 'home.project',
                        'display_name' => '项目管理',
                        'route' => 'home.project',
                        'child' => [
                            ['name' => 'home.project.create', 'display_name' => '添加','route'=>'home.project.create'],
                            ['name' => 'home.project.edit', 'display_name' => '编辑','route'=>'home.project.edit'],
                            ['name' => 'home.project.destroy', 'display_name' => '删除','route'=>'home.project.destroy'],
                            ['name' => 'home.project.show', 'display_name' => '详情','route'=>'home.project.show'],
                            ['name' => 'home.project.node', 'display_name' => '节点变更','route'=>'home.project.node'],
                            ['name' => 'home.project.remark', 'display_name' => '添加备注','route'=>'home.project.remark'],
                            ['name' => 'home.project.import', 'display_name' => '导入','route'=>'home.project.import'],
                            ['name' => 'home.project.downloadTemplate', 'display_name' => '下载模板','route'=>'home.project.downloadTemplate'],
                        ]
                    ],
                    [
                        'name' => 'home.remind',
                        'display_name' => '跟进提醒',
                        'route' => 'home.remind',
                        'child' => [

                        ]
                    ],
                    [
                        'name' => 'home.waste',
                        'display_name' => '回收站',
                        'route' => 'home.waste',
                        'child' => [
                            ['name' => 'home.waste.retrieve', 'display_name' => '拾回','route'=>'home.waste.retrieve'],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'home.monitor',
                'display_name' => '数据监控',
                'route' => '',
                'icon' => 'layui-icon-templeate-1',
                'child' => [
                    [
                        'name' => 'home.sip.count',
                        'display_name' => '分机统计',
                        'route' => 'home.sip.count',
                        'child' => [

                        ]
                    ],
                    [
                        'name' => 'home.cdr',
                        'display_name' => '通话记录',
                        'route' => 'home.cdr',
                        'child' => [
                            ['name' => 'home.cdr.play', 'display_name' => '录音播放','route'=>'home.cdr.play'],
                            ['name' => 'home.cdr.download', 'display_name' => '下载录音','route'=>'home.cdr.download'],
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
        }

        $roles = [
            [
                'name' => 'home_jinli',
                'display_name' => '经理',
                'guard_name' => 'merchant',
            ],
            [
                'name' => 'home_member',
                'display_name' => '员工',
                'guard_name' => 'merchant',
            ],
        ];
        foreach ($roles as $role){
            \App\Models\Role::create($role);
        }

    }
}
