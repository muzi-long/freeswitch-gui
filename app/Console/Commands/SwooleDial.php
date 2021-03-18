<?php

namespace App\Console\Commands;

use App\Models\Cdr;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Service\EslListen;

class SwooleDial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:dial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '呼叫队列';

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
            $key = config('freeswitch.redis_key.dial');
            while (true) {
                $uuid = Redis::lpop($key);
                if($uuid == null){
                    sleep(2);
                    continue;
                }
                //开户协程发起通话并监听

                    \Swoole\Coroutine::create(function () use ($uuid){
                        (new EslListen($uuid))->run();
                    });

            }
        });
    }
}
