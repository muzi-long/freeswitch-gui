<?php

namespace App\Console\Commands;

use App\Models\Sip;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use App\Service\SwooleFreeswitch;

class EslCustom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'esl:custom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '监听分机注册和注销';

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
        \Swoole\Coroutine\run(function () {
            $fs = new SwooleFreeswitch();
            $con = $fs->connect();
            if (!$con) {
                return false;
            }
            $fs->events("CUSTOM sofia::register sofia::unregister");
            while (true) {
                $received_parameters = $fs->recvEvent();
                if (!empty($received_parameters)) {
                    $info = $fs->serialize($received_parameters);
                    $eventname = Arr::get($info, "Event-Subclass"); //子事件名称
                    $eventname = urldecode($eventname);
                    $username = Arr::get($info, "username"); //分机号
                    switch ($eventname) {
                        //注册
                        case 'sofia::register':
                            Sip::query()->where('username', $username)->update(['status' => 1, 'state' => 'down']);
                            break;
                        //注销
                        case 'sofia::unregister':
                            Sip::query()->where('username', $username)->update(['status' => 0, 'state' => 'down']);
                            break;
                        default:
                            break;
                    }
                }
            }
            $fs->disconnect();
        });
    }
}
