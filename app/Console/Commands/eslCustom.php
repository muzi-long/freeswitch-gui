<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class eslCustom extends Command
{
    /**
     * 监听CUSTOM事件
     * @var string
     */
    protected $signature = 'esl:custom {event*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'esl listen custom events';


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
        $fs = new \Freeswitchesl();
        $service = [
            'host' => '127.0.0.1',
            'port' => 8022,
            'password' => 'dgg@1234.',
        ];
        if (!$fs->connect($service['host'], $service['port'], $service['password'])) {
            Log::error("asr监听ESL未连接");
            return false;
        }
        //======================  接收事件参数验证  ====================
        $eventarr = [
            'sofia::register',
            'sofia::unregister',
        ];
        $argument = $this->argument('event');
        foreach ($argument as $name) {
            if (!in_array($name, $eventarr)) {
                $this->error('custom event ' . $name . ' not allowed');
                return false;
            }
        }
        $event = implode(" ", $argument);
        //======================  接收事件参数验证  ====================

        $fs->events('plain', 'CUSTOM ' . $event);
        while (true) {
            $received_parameters = $fs->recvEvent();
            if (!empty($received_parameters)) {
                $info = $fs->serialize($received_parameters, "json");
                $info = json_decode($info, true);
                $eventname = Arr::get($info, "Event-Subclass"); //子事件名称
                $eventname = urldecode($eventname);
                $username = Arr::get($info, "username"); //分机号
                switch ($eventname) {
                    //注册
                    case 'sofia::register':
                        DB::table('sip')->where('username', $username)->update(['status' => 1]);
                        break;
                    //注销
                    case 'sofia::unregister':
                        DB::table('sip')->where('username', $username)->update(['status' => 0]);
                        break;
                    default:
                        break;
                }
            }
        }
        $fs->disconnect();
    }


}
