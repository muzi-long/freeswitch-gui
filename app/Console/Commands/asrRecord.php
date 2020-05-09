<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class asrRecord extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asr:record';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '从redis里取数据写入cdr';

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
        $key = 'asr_cdr_key';
        while (true){
            $item = Redis::lPop($key);
            if ($item){
                try{
                    $data = json_decode($item,true);
                    if ($data['leg_type']=='A'){
                        if (!$data['update_data']['src']) continue;
                        $model = DB::table('sip')
                            ->join('user','sip.id','=','user.sip_id')
                            ->where('sip.username',$data['update_data']['src'])
                            ->select(['user.id','user.depart_id'])
                            ->first();
                        if ($model == null) continue;
                        DB::table($data['table_name'])->updateOrInsert([
                            'uuid' => $data['uuid'],
                        ],array_merge($data['update_data'],[
                            'user_id' => $model->id,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]));
                    }else{
                        DB::table($data['table_name'])->updateOrInsert([
                            'uuid' => $data['uuid'],
                        ],$data['update_data']);
                    }
                }catch (\Exception $exception){
                    Log::info('写入通话记录异常：'.$exception->getMessage());
                }
                usleep(50);
            }else{
                sleep(10);
            }
        }
    }
}
