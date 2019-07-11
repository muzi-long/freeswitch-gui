<?php

namespace App\Console\Commands;

use App\Models\Call;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class asr_listen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asr:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'asr listen';

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
        //定义日志
        $monolog = Log::getMonolog();
        $monolog->popHandler();
        Log::useFiles(storage_path('logs/asr_listen.log'));

        $fs = new \Freeswitchesl();
        if (!$fs->connect(config('freeswitch.event_socket.host'), config('freeswitch.event_socket.port'), config('freeswitch.event_socket.password'))){
            echo "ESL未连接";
            return 1;
        }
        $fs->events('json', 'CUSTOM asr');
        while (true){
            $received_parameters = $fs->recvEvent();
            if (!empty($received_parameters)) {
                //记录日志
                $info = $fs->serialize($received_parameters,"json");
                Log::info($info);
                $response = $fs->getHeader($received_parameters,"ASR-Response");
                $response = json_decode(urldecode($response),true);
                if($response['status_code']==200&&isset($response['result'])&&$response['result']['status_code']==0&&!empty($response['result']['text'])){
                    $text = $response['result']['text'];
                    $uuid = $fs->getHeader($received_parameters,"Core-UUID");
                    DB::table('cdr_asr')->insert([
                        'uuid'          => $uuid,
                        'text'          => $text,
                        'created_at'    => Carbon::now(),
                        'updated_at'    => Carbon::now(),
                    ]);
                }
            }
        }
        $fs->disconnect();
    }
}
