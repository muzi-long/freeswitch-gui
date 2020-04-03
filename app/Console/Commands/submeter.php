<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class submeter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:submeter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动按月分表';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //表名
        $tables = ['asr','cdr'];
        foreach ($tables as $table){
            $new_table = $table.'_'.date('Ym',strtotime('+1 month'));
            try{
                if (!Schema::hasTable($new_table)) {
                    DB::update("create table {$new_table} like {$table}");
                }
            }catch (\Exception $exception){
                Log::info('创建分表'.$new_table.'异常：'.$exception->getMessage());
            }
        }
        //需要合建视图的表
        $view_tables = [];
        foreach ($view_tables as $table){
            $this->createView($table);
        }
    }

    /**
     * 创建视图
     * @param $table
     */
    public function createView($table)
    {
        try{
            //先删除视图
            try{
                DB::statement("drop view {$table}_view");
            }catch (\Exception $exception){
                Log::info('删除视图'.$table.'_view异常：'.$exception->getMessage());
            }

            //查询所有表
            $res = DB::select("show tables like '".$table."_%'");
            $tables = array_map(function($item) use($table){
                $name = collect(json_decode(json_encode($item), true))->first();
                if (preg_match('/'.$table.'_2\d{5}/',$name)){
                    return $name;
                }
            },$res);
            if (!empty($tables)){
                $sql = "CREATE VIEW {$table}_view AS ";
                foreach ($tables as $k => $tab){
                    $sql .= " (select * from ".$tab.") ";
                    if (count($tables) > 1 && $k < count($tables)-1){
                        $sql .= "union all";
                    }
                }
                DB::statement($sql);
            }
        }catch (\Exception $exception){
            Log::info('创建视图'.$table.'异常：'.$exception->getMessage());
        }
    }

}
