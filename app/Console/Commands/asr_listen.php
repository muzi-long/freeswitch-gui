<?php

namespace App\Console\Commands;

use App\Models\Sip;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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
    protected $fs_record = '/usr/local/freeswitch/recordings/';
    protected $asr_table = null;
    protected $asr_list_table = null;
    protected $cdr_a_leg_table = null;
    protected $cdr_b_leg_table = null;
    public $client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fs = new \Freeswitchesl();
        if (!$fs->connect(config('freeswitch.event_socket.host'), config('freeswitch.event_socket.port'), config('freeswitch.event_socket.password'))) {
            $this->error("ESL未连接");
            return 1;
        }
        $status = 1;
        $fs->events('plain', 'CHANNEL_ANSWER CHANNEL_HANGUP RECORD_START RECORD_STOP CHANNEL_BRIDGE CHANNEL_HANGUP_COMPLETE');
        while ($status == 1) {

            //录音目录
            $filepath = $this->fs_record . date('Y') . '/' . date('m') . '/' . date('d') . '/';
            if (!is_dir($filepath)) {
                mkdir($filepath, 0777, true);
            }
            $received_parameters = $fs->recvEvent();
            if (!empty($received_parameters)) {
                //检测分表
                $this->autoSubmeter();

                //记录日志
                $info = $fs->serialize($received_parameters, "json");

                $eventname = $fs->getHeader($received_parameters, "Event-Name"); //事件名称
                $uuid = $fs->getHeader($received_parameters, "Unique-ID");//当前信道leg的uuid
                $CallerCallerIDNumber = $fs->getHeader($received_parameters, "Caller-Caller-ID-Number");//打电话者
                $CallerCalleeIDNumber = $fs->getHeader($received_parameters, "Caller-Callee-ID-Number");//接电话者

                if ($eventname == 'CHANNEL_ANSWER') {
                    //$this->info($info);
                    $otherUuid = $fs->getHeader($received_parameters, "Other-Leg-Unique-ID");
                    if ($otherUuid) { //被叫应答后
                        //设置费率
                        $sip = Sip::with(['rate', 'merchant'])->where('username', $CallerCallerIDNumber)->first();
                        if ($sip != null && isset($sip->rate->cost1) && isset($sip->merchant->id)) {
                            $fs->bgapi("uuid_setvar {$uuid} nibble_rate {$sip->rate->cost1}");
                            $fs->bgapi("uuid_setvar {$uuid} nibble_account {$sip->merchant->id}");
                            $fs->bgapi("uuid_setvar {$uuid} nibble_increment {$sip->rate->cycle}");
                        }
                        //开启全程录音
                        $fullfile = $filepath . 'full_' . md5($otherUuid . $uuid) . '.wav';
                        if (!file_exists($fullfile)) {
                            $fs->bgapi("uuid_record {$uuid} start {$fullfile} 7200"); //录音
                            $fs->bgapi("uuid_setvar {$uuid} record_file {$fullfile}");//设置录音地址变量
                            $fs->bgapi("uuid_setvar {$otherUuid} record_file {$fullfile}");//设置录音地址变量

                            //写入记录
                            DB::table($this->asr_table)->insert([
                                'id' => \Snowflake::nextId(),
                                'src' => $CallerCallerIDNumber,
                                'dst' => $CallerCalleeIDNumber,
                                'auuid' => $otherUuid,
                                'buuid' => $uuid,
                                'record_file' => $fullfile,
                                'created_at' => Carbon::now(),
                            ]);
                        }

                        //开启分段录音A
                        $halffile_a = $filepath . 'half_' . md5($otherUuid . time() . uniqid()) . '.wav';
                        $fs->bgapi("uuid_record " . $otherUuid . " start " . $halffile_a . " 18");
                        //记录分段录音数据
                        $this->channel[$otherUuid] = [
                            'pid' => null,
                            'unique_id' => $otherUuid,
                            'record_file' => $halffile_a,
                            'created_at' => Carbon::now(),
                        ];

                        //开启分段录音B
                        $halffile_b = $filepath . 'half_' . md5($uuid . time() . uniqid()) . '.wav';
                        $fs->bgapi("uuid_record " . $uuid . " start " . $halffile_b . " 18");
                        //记录分段录音数据
                        $this->channel[$uuid] = [
                            'pid' => null,
                            'unique_id' => $uuid,
                            'record_file' => $halffile_b,
                            'created_at' => Carbon::now(),
                        ];
                    }

                } elseif ($eventname == 'CHANNEL_BRIDGE') {
                    //$this->info($info);

                } elseif ($eventname == 'RECORD_START')//开始说话
                {

                } elseif ($eventname == 'RECORD_STOP')//结束说话
                {
                    if (isset($this->channel[$uuid])) {
                        $halffile = $this->channel[$uuid]['record_file'];
                        if (file_exists($halffile)) {
                            //语音识别 $this->channel[$uuid]['record_file'] 文件
                            $fileurl = str_replace('/usr/local/freeswitch', config('app.url'), $this->channel[$uuid]['record_file']);
                            $res = file_get_contents('http://106.13.221.138:188?fileurl=' . $fileurl);
                            $result = json_decode($res, true);
                            if ($result['status'] == 1) {
                                $text = $result['asr'];
                                //记录并推送websocket消息
                                try {
                                    DB::table($this->asr_list_table)->insert([
                                        'id' => \Snowflake::nextId(),
                                        'pid' => $this->channel[$uuid]['pid'],
                                        'unique_id' => $this->channel[$uuid]['unique_id'],
                                        'record_file' => $this->channel[$uuid]['record_file'],
                                        'created_at' => $this->channel[$uuid]['created_at'],
                                        'text' => $text,
                                    ]);
                                    $pushData = [
                                        'code' => 0,
                                        'msg' => '语音识别文本',
                                        'data' => [
                                            'sip' => $CallerCallerIDNumber,
                                            'text' => $text,
                                            'uuid' => $uuid
                                        ],
                                    ];
                                    $this->client->post('http://127.0.0.1:9501', ['timeout' => 1, 'form_params' => ['data' => json_encode($pushData)]]);
                                } catch (\Exception $exception) {
                                    Log::info("识别记录：" . $exception->getMessage());
                                }
                            }
                        }
                        //结束说话 后接着开启分段录音
                        $halffile = $filepath . 'half_' . md5($uuid . time() . uniqid()) . '.wav';
                        $this->channel[$uuid]['record_file'] = $halffile;
                        $this->channel[$uuid]['created_at'] = Carbon::now();
                        $fs->bgapi("uuid_record " . $uuid . " start " . $halffile . " 18");
                    }
                } elseif ($eventname == 'CHANNEL_HANGUP') {
                    //$this->info('CHANNEL_HANGUP事件：'.$uuid);
                    if (isset($this->channel[$uuid])) {
                        unset($this->channel[$uuid]);
                    }
                } elseif ($eventname == 'CHANNEL_HANGUP_COMPLETE') {
                    //$this->info($info);
                    //推送websocket消息
                    try {
                        $pushData = [
                            'code' => 2,
                            'msg' => '挂机',
                            'data' => [
                                'sip' => $CallerCallerIDNumber,
                                'uuid' => $uuid
                            ],
                        ];
                        $this->client->post('http://127.0.0.1:9501', ['timeout' => 1, 'form_params' => ['data' => json_encode($pushData)]]);
                    } catch (\Exception $exception) {

                    }
                    //执行分表，记录通话记录
                    $this->autoSubmeterCdr();
                    $otherType = $fs->getHeader($received_parameters, "Other-Type");
                    $thoerLegUniqueId = $fs->getHeader($received_parameters, "Other-Leg-Unique-ID");
                    $start = $fs->getHeader($received_parameters, "variable_start_stamp");
                    $answer = $fs->getHeader($received_parameters, "variable_answer_stamp");
                    $end = $fs->getHeader($received_parameters, "variable_end_stamp");
                    $cdr = [
                        'caller_id_number' => $CallerCallerIDNumber,
                        'destination_number' => $CallerCalleeIDNumber,
                        'duration' => $fs->getHeader($received_parameters, "variable_duration"),
                        'billsec' => $fs->getHeader($received_parameters, "variable_billsec"),
                        'start_stamp' => $start ? urldecode($start) : null,
                        'answer_stamp' => $answer ? urldecode($answer) : null,
                        'end_stamp' => $end ? urldecode($end) : null,
                        'hangup_cause' => $fs->getHeader($received_parameters, "variable_hangup_cause"),
                        'record_file' => $fs->getHeader($received_parameters, "variable_record_file"),
                        'nibble_total_billed' => (int)$fs->getHeader($received_parameters, "variable_nibble_total_billed"),
                        'extend_content' => $fs->getHeader($received_parameters, "variable_extend_content"),
                    ];
                    if (empty($otherType) || $otherType == 'originatee') {
                        $cdr['aleg_uuid'] = $uuid;
                        $cdr['bleg_uuid'] = $thoerLegUniqueId;
                        $table = $this->cdr_a_leg_table;
                    } else {
                        $cdr['aleg_uuid'] = $thoerLegUniqueId;
                        $cdr['bleg_uuid'] = $uuid;
                        $table = $this->cdr_b_leg_table;
                    }
                    DB::table($table)->insert($cdr);
                }
            }
        }
        $fs->disconnect();
    }

    /**
     * 自动分表
     */
    public function autoSubmeter()
    {
        $this->asr_table = 'asr_' . date('Ym');
        $this->asr_list_table = 'asr_list_' . date('Ym');
        if (!Schema::hasTable($this->asr_table)) {
            DB::update("create table " . $this->asr_table . " like asr");
        }
        if (!Schema::hasTable($this->asr_list_table)) {
            DB::update("create table " . $this->asr_list_table . " like asr_list");
        }
    }

    /**
     * cdr自动分表
     */
    public function autoSubmeterCdr()
    {
        $this->cdr_a_leg_table = 'cdr_a_leg_' . date('Ym');
        $this->cdr_b_leg_table = 'cdr_b_leg_' . date('Ym');
        if (!Schema::hasTable($this->cdr_a_leg_table)) {
            DB::update("create table " . $this->cdr_a_leg_table . " like cdr_a_leg");
        }
        if (!Schema::hasTable($this->cdr_b_leg_table)) {
            DB::update("create table " . $this->cdr_b_leg_table . " like cdr_b_leg");
        }
    }

}
