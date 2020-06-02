<?php

use Illuminate\Database\Seeder;

class ProjectTableSeeder extends Seeder
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
        \Illuminate\Support\Facades\DB::table('node')->truncate();
        \Illuminate\Support\Facades\DB::table('project_design_value')->truncate();
        \Illuminate\Support\Facades\DB::table('project_design')->truncate();
        \Illuminate\Support\Facades\DB::table('project_node')->truncate();
        \Illuminate\Support\Facades\DB::table('project_remark')->truncate();
        \Illuminate\Support\Facades\DB::table('project')->truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        //填充一些节点
        $nodes = [
            ['name' => '项目启动'],
            ['name' => '做资料'],
            ['name' => '等待发章'],
            ['name' => '移交客户'],
            ['name' => '收尾款'],
            ['name' => '完结'],
        ];
        foreach ($nodes as $node){
            \App\Models\Node::create($node);
        }
        //填充一些项目
        for ($i=1;$i<=10;$i++){
            \App\Models\Project::create([
                'company_name' => \Faker\Provider\zh_CN\Company::companySuffix(),
                'name' => \Faker\Provider\zh_CN\Person::firstNameMale(),
                'phone' => '1'.mt_rand(3,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9),
                'node_id' => mt_rand(1,5),
                'created_user_id' => 1,
                'updated_user_id' => 11,
                'owner_user_id' => 1,
            ]);
        }
    }
}
