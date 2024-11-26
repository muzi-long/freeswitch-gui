<?php

use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //创建一些角色
        $roles = [
            [
                'name' => 'admin',
                'display_name' => '管理员',
            ],
            [
                'name' => 'manager',
                'display_name' => '经理',
            ],
            [
                'name' => 'staff',
                'display_name' => '员工'
            ],
        ];
        foreach ($roles as $role){
            \App\Models\Role::create($role);
        }
        //创建一些用户
        $users = [
            [
                'name' => 'admin',
                'password' => bcrypt('123456'),
                'nickname' => '系统管理员',
                'phone' => '13000000000',
            ],
            [
                'name' => 'test',
                'password' => bcrypt('123456'),
                'nickname' => '测试人员',
                'phone' => '13111111111',
            ],
        ];
        foreach ($users as $user){
            \App\Models\User::create($user);
        }
        //创建一些节点
        $nodes = [
            [
                'name' => '完结',
                'sort' => 99,
                'type' => 1,
            ],
            [
                'name' => '初步沟通',
                'sort' => 1,
                'type' => 2,
            ],
            [
                'name' => '邀约上门',
                'sort' => 2,
                'type' => 2,
            ],
            [
                'name' => '签合同',
                'sort' => 3,
                'type' => 2,
            ],
        ];
        foreach ($nodes as $node){
            \App\Models\Node::create($node);
        }
        //创建一些客户信息
        factory(\App\Models\Customer::class)->times(300)->make()->each(function ($model) {
            // 数据入库
            $model->save();
        });

    }
}
