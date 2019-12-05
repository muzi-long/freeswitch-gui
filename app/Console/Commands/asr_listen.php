<?php

namespace App\Console\Commands;

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

    protected $channel;
    protected $fs_record = '/usr/local/freeswitch/var/lib/freeswitch/recordings/';

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
        if (!$fs->connect('127.0.0.1', '8022', 'dgg@1234.')){
            $this->error("ESL未连接");
            return 1;
        }
        $status = 1;
        $fs->events('plain','CHANNEL_ANSWER CHANNEL_HANGUP RECORD_START RECORD_STOP');
        while ($status==1){
            //录音目录
            $filepath = $this->fs_record.date('Y').'/'.date('m').'/'.date('d').'/';
            if (!is_dir($filepath)) {
                mkdir($filepath,0777,true);
            }
            $received_parameters = $fs->recvEvent();
            if (!empty($received_parameters)) {
                //记录日志
                $info = $fs->serialize($received_parameters,"json");

                $eventname = $fs->getHeader($received_parameters,"Event-Name"); //事件名称
                $uuid = $fs->getHeader($received_parameters,"Unique-ID");//当前信道leg的uuid
                $CallerOrigCallerIDNumber = $fs->getHeader($received_parameters,"Caller-Orig-Caller-ID-Number");//打电话者
                $CallerCalleeIDNumber = $fs->getHeader($received_parameters,"Caller-Callee-ID-Number");//接电话者

                if ($eventname == 'CHANNEL_ANSWER')
                {
                    $this->info('CHANNEL_ANSWER事件：'.$uuid);

                    //开启全程录音
                    $fullfile = $filepath.md5($uuid.uniqid()).'.wav';
                    if (!file_exists($fullfile)){
                        $fs->bgapi("uuid_record ".$uuid." start ".$fullfile." 7200"); //收到应答开启全程录音
                    }

                    //写入记录
                    DB::table('asr')->insert([
                        'src' => $CallerOrigCallerIDNumber,
                        'dst' => $CallerCalleeIDNumber,
                        'unique_id' => $uuid,
                        'record_file' => $fullfile,
                        'created_at' => Carbon::now(),
                    ]);

                    //开启分段录音
                    $halffile = $filepath.md5($uuid.time().uniqid()).'.wav';
                    $fs->bgapi("uuid_record ".$uuid." start ".$halffile." 18");
                    //记录分段录音数据
                    $this->channel[$uuid] = [
                        'pid' => null,
                        'unique_id' => $uuid,
                        'record_file' => $halffile,
                        'created_at' => Carbon::now(),
                    ];

                }elseif ($eventname == 'RECORD_START')//开始说话
                {

                }elseif ($eventname == 'RECORD_STOP')//结束说话
                {
                    if (isset($this->channel[$uuid])){
                        $halffile = $this->channel[$uuid]['record_file'];
                        if (file_exists($halffile)) {
                            //在这里进行语音识别 $this->channel[$uuid]['record_file'] 文件

                            $text = null;
                            DB::table('asr_list')->insert([
                                'pid' => $this->channel[$uuid]['pid'],
                                'unique_id' => $this->channel[$uuid]['unique_id'],
                                'record_file' => $this->channel[$uuid]['record_file'],
                                'created_at' => $this->channel[$uuid]['created_at'],
                                'text' => $text,
                            ]);
                        }
                        //结束说话 后接着开启分段录音
                        $halffile = $filepath.md5($uuid.time().uniqid()).'.wav';
                        $this->channel[$uuid]['record_file'] = $halffile;
                        $this->channel[$uuid]['created_at'] = Carbon::now();
                        $fs->bgapi("uuid_record ".$uuid." start ".$halffile." 18");
                    }
                }elseif ($eventname == 'CHANNEL_HANGUP')
                {
                    $this->info('CHANNEL_HANGUP事件：'.$uuid);
                    if (isset($this->channel[$uuid])){
                        unset($this->channel[$uuid]);
                    }
                }
            }
        }
        $fs->disconnect();
    }
}
