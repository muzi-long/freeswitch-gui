<?php

use Illuminate\Database\Seeder;

class MenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('menu')->truncate();
        $datas = [
            [
                'name' => '系统管理',
                'route' => null,
                'url' => null,
                'icon' => 'layui-icon-auz',
                'type' => 2,
                'sort' => 1,
                'permission_name' => 'system',
                'child' => [
                    [
                        'name' => '用户管理',
                        'route' => 'admin.user',
                        'url' => null,
                        'icon' => 'layui-icon-username',
                        'type' => 1,
                        'permission_name' => 'system.user',
                    ],
                    [
                        'name' => '角色管理',
                        'route' => 'admin.role',
                        'url' => null,
                        'icon' => 'layui-icon-group',
                        'type' => 1,
                        'permission_name' => 'system.role',
                    ],
                    [
                        'name' => '权限管理',
                        'route' => 'admin.permission',
                        'url' => null,
                        'icon' => 'layui-icon-key',
                        'type' => 1,
                        'permission_name' => 'system.permission',
                    ],
                    [
                        'name' => '菜单管理',
                        'route' => 'admin.menu',
                        'url' => null,
                        'icon' => 'layui-icon-menu-fill',
                        'type' => 1,
                        'permission_name' => 'system.menu',
                    ],
                ]
            ],
            [
                'name' => '服务配置',
                'route' => null,
                'url' => null,
                'icon' => 'layui-icon-windows',
                'type' => 2,
                'sort' => 2,
                'permission_name' => 'fs',
                'child' => [
                    [
                        'name' => '分机管理',
                        'route' => 'admin.sip',
                        'url' => null,
                        'icon' => 'layui-icon-cellphone',
                        'type' => 1,
                        'permission_name' => 'fs.sip',
                    ],
                    [
                        'name' => '网关管理',
                        'route' => 'admin.gateway',
                        'url' => null,
                        'icon' => 'layui-icon-service',
                        'type' => 1,
                        'permission_name' => 'fs.gateway',
                    ],
                    [
                        'name' => '出局号码',
                        'route' => 'admin.gateway_outbound',
                        'url' => null,
                        'icon' => 'layui-icon-link',
                        'type' => 1,
                        'permission_name' => 'fs.gateway_outbound',
                    ],
                    [
                        'name' => '拨号计划',
                        'route' => 'admin.extension',
                        'url' => null,
                        'icon' => 'layui-icon-chart',
                        'type' => 1,
                        'permission_name' => 'fs.extension',
                    ],
                ]
            ],
            [
                'name' => '群呼管理',
                'route' => null,
                'url' => null,
                'icon' => 'layui-icon-group',
                'type' => 2,
                'sort' => 2,
                'permission_name' => 'callcenter',
                'child' => [
                    [
                        'name' => '坐席管理',
                        'route' => 'admin.agent',
                        'url' => null,
                        'icon' => 'layui-icon-friends',
                        'type' => 1,
                        'permission_name' => 'callcenter.agent',
                    ],
                    [
                        'name' => '队列管理',
                        'route' => 'admin.queue',
                        'url' => null,
                        'icon' => 'layui-icon-user',
                        'type' => 1,
                        'permission_name' => 'callcenter.queue',
                    ],
                    [
                        'name' => '任务管理',
                        'route' => 'admin.task',
                        'url' => null,
                        'icon' => 'layui-icon-template-1',
                        'type' => 1,
                        'permission_name' => 'callcenter.task',
                    ],
                ]
            ],
            [
                'name' => 'CRM管理',
                'route' => null,
                'url' => null,
                'icon' => 'layui-icon-android',
                'type' => 2,
                'sort' => 2,
                'permission_name' => 'crm',
                'child' => [
                    [
                        'name' => '部门管理',
                        'route' => 'admin.department',
                        'url' => null,
                        'icon' => 'layui-icon-group',
                        'type' => 1,
                        'permission_name' => 'crm.department',
                    ],
                    [
                        'name' => '节点管理',
                        'route' => 'admin.node',
                        'url' => null,
                        'icon' => 'layui-icon-cellphone-fine',
                        'type' => 1,
                        'permission_name' => 'crm.node',
                    ],
                    [
                        'name' => '客户配置',
                        'route' => 'admin.project-design',
                        'url' => null,
                        'icon' => 'layui-icon-set-fill',
                        'type' => 1,
                        'permission_name' => 'crm.project-design',
                    ],
                    [
                        'name' => '客户管理',
                        'route' => 'admin.project',
                        'url' => null,
                        'icon' => 'layui-icon-reply-fill',
                        'type' => 1,
                        'permission_name' => 'crm.project',
                    ],
                    [
                        'name' => '公海库',
                        'route' => 'admin.waste',
                        'url' => null,
                        'icon' => 'layui-icon-404',
                        'type' => 1,
                        'permission_name' => 'crm.waste',
                    ],
                    [
                        'name' => '跟进提醒',
                        'route' => 'admin.remind',
                        'url' => null,
                        'icon' => 'layui-icon-dialogue',
                        'type' => 1,
                        'permission_name' => 'crm.remind',
                    ],
                ]
            ],
            [
                'name' => '数据监控',
                'route' => null,
                'url' => null,
                'icon' => 'layui-icon-slider',
                'type' => 2,
                'sort' => 2,
                'permission_name' => 'data',
                'child' => [
                    [
                        'name' => '通话记录',
                        'route' => 'admin.cdr',
                        'url' => null,
                        'icon' => 'layui-icon-service',
                        'type' => 1,
                        'permission_name' => 'data.cdr',
                    ],
                    [
                        'name' => '呼叫统计',
                        'route' => 'admin.cdr.count',
                        'url' => null,
                        'icon' => 'layui-icon-ios',
                        'type' => 1,
                        'permission_name' => 'data.cdr.count',
                    ],
                    [
                        'name' => '语音合成',
                        'route' => 'admin.audio',
                        'url' => null,
                        'icon' => 'layui-icon-mike',
                        'type' => 1,
                        'permission_name' => 'data.audio',
                    ],
                    [
                        'name' => '分机监控',
                        'route' => 'admin.monitor',
                        'url' => null,
                        'icon' => 'layui-icon-link',
                        'type' => 1,
                        'permission_name' => 'data.monitor',
                    ],
                ]
            ],
            [
                'name' => '接口文档',
                'route' => null,
                'url' => null,
                'icon' => 'layui-icon-file-b',
                'type' => 2,
                'sort' => 2,
                'permission_name' => 'api',
                'child' => [
                    [
                        'name' => '文档列表',
                        'route' => null,
                        'url' => '/apidoc',
                        'icon' => 'layui-icon-form',
                        'type' => 1,
                        'permission_name' => 'api.list',
                    ],
                ]
            ],
        ];
        $permissions = \App\Models\Permission::pluck('id','name')->toArray();
        foreach ($datas as $k1 => $d1){
            $m1 = \App\Models\Menu::create([
                'name' => $d1['name'],
                'route' => $d1['route'],
                'url' => $d1['url'],
                'icon' => $d1['icon'],
                'type' => $d1['type'],
                'sort' => $k1+1,
                'permission_id' => \Illuminate\Support\Arr::get($permissions,$d1['permission_name'],null),
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
                    ]);
                }
            }
        }
    }
}
