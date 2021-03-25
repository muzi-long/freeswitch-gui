<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class SwooleWebsocket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:websocket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'websocket服务';

    public $ws;
    public $fd = [];

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
        $this->ws = new \Swoole\WebSocket\Server('0.0.0.0', 9502);
        $this->ws->set(['worker_num' => 1]);
        $this->ws->on('open', [$this, 'open']);
        $this->ws->on('message', [$this, 'message']);
        $this->ws->on('request', [$this, 'request']);
        $this->ws->on('close', [$this, 'close']);
        $this->ws->start();
    }

    public function open($server, $request)
    {
        $user_id = Arr::get($request->get,'user_id');
        $this->fd[$request->fd] = $user_id;
    }

    public function message($server, $request)
    {
        $server->push($request->fd,$request->data);
    }

    public function request($request, $response)
    {
        $parms = $request->getContent();
        $data = Arr::get($parms, 'data');
        $user_ids = Arr::get($parms, 'user_ids',[]);
        if ($data != null && !empty($user_ids)) {
            foreach ($this->fd as $k => $v) {
                // 需要先判断是否是正确的websocket连接，否则有可能会push失败
                if ($this->ws->isEstablished($k)) {
                    if (in_array($v,$user_ids)){
                        $this->ws->push($k, $data);
                    }
                }
            }
        }
        $response->end("");
    }

    public function close($server, $fd)
    {
        //清除键值对，防止内存溢出
        foreach ($this->fd as $k => $v) {
            if ($fd == $v) {
                unset($this->fd[$v]);
                break;
            }
        }
    }


}
