<?php

namespace App\Console\Commands;

use App\Models\Cdr;
use App\Service\SwooleFreeswitch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Service\EslListen;

class SwooleApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '执行fs api';

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
            $key = config('freeswitch.redis_key.api');
            while (true) {
                $dial_str = Redis::lpop($key);
                if($dial_str == null){
                    sleep(2);
                    continue;
                }
                //开户协程处理
                \Swoole\Coroutine::create(function () use ($dial_str){
                    try {
                        $fs = new SwooleFreeswitch();
                        if (!$fs->connect()) {
                            return false;
                        }
                        $fs->bgapi($dial_str);
                        $fs->disconnect();
                    }catch (\Exception $exception){
                        Log::error('swoole api 执行异常：'.$exception->getMessage());
                    }
                });
            }
        });
    }
}
