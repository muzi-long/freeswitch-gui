<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Cdr;
use App\Models\Aleg;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class cdrSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cdr:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步AB通话记录到cdr';

    protected $records_table = null;
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
     * 同步通话记录cdr_a_leg、records
     * @return mixed
     */
    public function handle()
    {

        $res = Aleg::with('bleg')->where('is_save',0)->orderBy('id','asc')->get();
        $saveData = [];
        $idArr = [];
        foreach ($res as $key => $value) {
            
            $saveData[] = [
                'uniqueid' => $item->aleg_uuid,
                'src' => $item->caller_id_number,
                'dst' => $item->destination_number,
                'billsec' => $item->bleg_uuid ? $item->bleg->billsec : 0,
                'record_file' => $item->record_file,
                'user_data' => $item->user_data,
                'hangup_cause' => $item->hangup_cause,
                'aleg_start_at' => $item->start_stamp,
                'aleg_answer_at' => $item->answer_stamp,
                'aleg_end_at' => $item->end_stamp,
                'bleg_start_at' => $item->bleg_uuid ? $item->bleg->start_stamp : null,
                'bleg_answer_at' => $item->bleg_uuid ? $item->bleg->answer_stamp : null,
                'bleg_end_at' => $item->bleg_uuid ? $item->bleg->end_stamp : null,
                'created_at' => Carbon::now(),

            ]
            $idArr[] = $item->id;
        }
        
        //开启事务
        DB::beginTransaction();
        try{
            foreach ($saveData as $key => $data) {
                DB::table('cdr')->insert($data);
            }
            DB::table('cdr_a_leg_')->whereIn('id',$idArr)->update(['is_save' => 1]);
            DB::commit();
        }catch (\Exception $exception) {
            DB::rollback();
            Log::info('同步通话记录失败：'.$exception->getMessage());
        }
    }

}
