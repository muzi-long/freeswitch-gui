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
