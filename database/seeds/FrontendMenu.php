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
                        'permission_name' => 'backend.call.sip',
                    ],
                    [
                        'name' => '我的分机',
                        'route' => 'frontend.call.sip.mine',
                        'url' => null,
                        'icon' => 'layui-icon-heart-fill',
                        'type' => 1,
                        'permission_name' => 'backend.call.sip.mine',
                    ],
                    [
                        'name' => '通话记录',
                        'route' => 'frontend.call.cdr',
                        'url' => null,
                        'icon' => 'layui-icon-service',
                        'type' => 1,
                        'permission_name' => 'backend.call.cdr',
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
