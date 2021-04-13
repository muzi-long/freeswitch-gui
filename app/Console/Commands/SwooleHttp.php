<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SwooleHttp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:http';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'swoole http';

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
        $http = new \Swoole\Http\Server("127.0.0.1", 9501);
        $http->set([
            'worker_num' => 1,
        ]);
        $conf = [
            'host' => config('freeswitch.esl.host'),
            'port' => config('freeswitch.esl.port'),
            'password' => config('freeswitch.esl.password'),
            'fs_cli_bin' => '/usr/local/freeswitch/bin/fs_cli',
            'gateway' => '/usr/local/freeswitch/conf/sip_profiles/external/',
            'directory' => '/usr/local/freeswitch/conf/directory/default/',
            //拨号计划下面默认分为default(呼出)和public(呼入)
            'dialplan' => '/usr/local/freeswitch/conf/dialplan/',
            'callcenter' => '/usr/local/freeswitch/conf/autoload_configs/callcenter.conf.xml'
        ];
        $http->on('request', function ($request, $response) use ($conf) {
            if($request->server['request_method'] == 'POST'){
                $data = json_decode($request->getContent(),true);
                $uri = $request->server['request_uri'];
                $return = ['code'=>1,'msg'=>''];
                $command = $conf['fs_cli_bin']." -H ".$conf['host']." -P ".$conf['port']." -p ".$conf['password']." -x ";
                try{
                    switch($uri){
                        case '/gateway': //网关
                            //清空所有文件
                            array_map('unlink', glob($conf['gateway']."*"));
                            //再写入
                            foreach ($data as $d){
                                $xml  = "<include>\n";
                                $xml .= "\t<gateway name=\"gw".$d['id']."\">\n";
                                $xml .= "\t\t<param name=\"realm\" value=\"".$d['realm']."\"/>\n";
                                if ($d['type'] == 1){ //sip对接
                                    $xml .= "\t\t<param name=\"username\" value=\"".$d['username']."\"/>\n";
                                    $xml .= "\t\t<param name=\"password\" value=\"".$d['password']."\"/>\n";
                                    $xml .= "\t\t<param name=\"register\" value=\"true\"/>\n";
                                }else{ //ip对接
                                    $xml .= "\t\t<param name=\"proxy\" value=\"".$d['realm']."\"/>\n";
                                    $xml .= "\t\t<param name=\"register\" value=\"false\"/>\n";
                                }
                                $xml .= "\t\t<param name=\"caller-id-in-from\" value=\"true\"/>\n";
                                $xml .= "\t</gateway>\n";
                                $xml .= "</include>";
                                file_put_contents($conf['gateway']."gw".$d['id'].".xml",$xml);
                            }
                            exec($command."\""."sofia profile external restart"."\"");
                            $return = ['code'=>0,'msg'=>'网关更新成功'];
                            break;
                        case '/directory': //分机
                            //清空所有文件
                            array_map('unlink', glob($conf['directory']."*"));
                            //再写入
                            foreach($data as $d){
                                $xml  = "<include>\n";
                                $xml .= "\t<user id=\"".$d['username']."\">\n";
                                $xml .= "\t\t<params>\n";
                                $xml .= "\t\t\t<param name=\"password\" value=\"".$d['password']."\"/>\n";
                                $xml .= "\t\t\t<param name=\"vm-password\" value=\"".$d['username']."\"/>\n";
                                $xml .= "\t\t</params>\n";
                                $xml .= "\t\t<variables>\n";
                                $xml .= "\t\t\t<variable name=\"toll_allow\" value=\"domestic,international,local\"/>\n";
                                $xml .= "\t\t\t<variable name=\"accountcode\" value=\"".$d['username']."\"/>\n";
                                $xml .= "\t\t\t<variable name=\"user_context\" value=\"default\"/>\n";
                                $xml .= "\t\t\t<variable name=\"effective_caller_id_name\" value=\"".$d['username']."\"/>\n";
                                $xml .= "\t\t\t<variable name=\"effective_caller_id_number\" value=\"".$d['username']."\"/>\n";
                                $xml .= "\t\t\t<variable name=\"callgroup\" value=\"techsupport\"/>\n";
                                $xml .= "\t\t</variables>\n";
                                $xml .= "\t</user>\n";
                                $xml .= "</include>\n";
                                file_put_contents($conf['directory'].$d['username'].".xml",$xml);
                            }
                            exec($command."\""."reloadxml"."\"");
                            $return = ['code'=>0,'msg'=>'分机更新成功'];
                            break;
                        case '/dialplan': //拨号计划
                            foreach($data as $context=>$extension){
                                $xml  = "<include>\n";
                                $xml .= "\t<context name=\"".$context."\">\n";
                                foreach ($extension as $exten){
                                    $xml .= "\t\t<extension name=\"" . $exten['name'] . "\" continue=\"" . $exten['continue'] . "\" >\n";
                                    if(isset($exten['condition']) && !empty($exten['condition'])){
                                        foreach($exten['condition'] as $condition){
                                            if(isset($condition['action']) && !empty($condition['action'])){
                                                $xml .= "\t\t\t<condition field=\"" . $condition['field'] . "\" expression=\"" . $condition['expression'] . "\" break=\"" . $condition['break'] . "\">\n";
                                                foreach ($condition['action'] as $action){
                                                    $xml .= "\t\t\t\t<action application=\"" . $action['application'] . "\" data=\"" . $action['data'] . "\" />\n";
                                                }
                                                $xml .= "\t\t\t</condition>\n";
                                            }else{
                                                $xml .= "\t\t\t<condition field=\"" . $condition['field'] . "\" expression=\"" . $condition['expression'] . "\" break=\"" . $condition['break'] . "\" />\n";
                                            }
                                        }
                                    }
                                    $xml .= "\t\t</extension>\n";
                                }
                                $xml .= "\t</context>\n";
                                $xml .= "</include>\n";
                                file_put_contents($conf['dialplan'].$context.".xml",$xml);
                            }
                            exec($command."\""."reloadxml"."\"");
                            $return = ['code'=>0,'msg'=>'拨号计划更新成功'];
                            break;
                        case '/callcenter':
                            $xml  = "<configuration name=\"callcenter.conf\" description=\"CallCenter\">\n";
                            $xml .= "\t<settings>\n";
                            $xml .= "\t\t<!--<param name=\"odbc-dsn\" value=\"dsn:user:pass\"/>-->\n";
                            $xml .= "\t\t<!--<param name=\"dbname\" value=\"/dev/shm/callcenter.db\"/>-->\n";
                            $xml .= "\t\t<!--<param name=\"cc-instance-id\" value=\"single_box\"/>-->\n";
                            $xml .= "\t\t<param name=\"truncate-tiers-on-load\" value=\"true\"/>\n";
                            $xml .= "\t\t<param name=\"truncate-agents-on-load\" value=\"true\"/>\n";
                            $xml .= "\t</settings>\n";
                            //----------------------------------  写入队列信息 ------------------------------------
                            $xml .= "\t<queues>\n";
                            foreach ($data['queues'] as $queue){
                                $xml .= "\t\t<queue name=\"queue".$queue['id']."\">\n";
                                $xml .= "\t\t\t<param name=\"strategy\" value=\"".$queue['strategy']."\"/>\n";
                                $xml .= "\t\t\t<param name=\"moh-sound\" value=\"\$\${hold_music}\"/>\n";
                                //$xml .= "\t\t\t<param name=\"record-template\" value=\"\$\${recordings_dir}/\${strftime(%Y)}/\${strftime(%m)}/\${strftime(%d)}/.\${destination_number}.\${caller_id_number}.\${uuid}.wav\"/>\n";
                                $xml .= "\t\t\t<param name=\"time-base-score\" value=\"system\"/>\n";
                                $xml .= "\t\t\t<param name=\"max-wait-time\" value=\"".$queue['max_wait_time']."\"/>\n";
                                $xml .= "\t\t\t<param name=\"max-wait-time-with-no-agent\" value=\"0\"/>\n";
                                $xml .= "\t\t\t<param name=\"max-wait-time-with-no-agent-time-reached\" value=\"5\"/>\n";
                                $xml .= "\t\t\t<param name=\"tier-rules-apply\" value=\"false\"/>\n";
                                $xml .= "\t\t\t<param name=\"tier-rule-wait-second\" value=\"300\"/>\n";
                                $xml .= "\t\t\t<param name=\"tier-rule-wait-multiply-level\" value=\"true\"/>\n";
                                $xml .= "\t\t\t<param name=\"tier-rule-no-agent-no-wait\" value=\"false\"/>\n";
                                $xml .= "\t\t\t<param name=\"discard-abandoned-after\" value=\"60\"/>\n";
                                $xml .= "\t\t\t<param name=\"abandoned-resume-allowed\" value=\"false\"/>\n";
                                $xml .= "\t\t</queue>\n";
                            }
                            $xml .= "\t</queues>\n";

                            //----------------------------------  写入坐席信息 ------------------------------------
                            $xml .= "\t<agents>\n";
                            foreach ($data['agents'] as $agent){
                                $contact = "[leg_timeout=10]user/".$agent['username'];
                                $xml .= "\t\t<agent name=\"agent".$agent['id']."\" type=\"callback\" contact=\"".$contact."\" status=\"".$agent['status']."\" max-no-answer=\"0\" wrap-up-time=\"10\" reject-delay-time=\"0\" busy-delay-time=\"0\" no-answer-delay-time=\"0\" />\n";
                            }
                            $xml .= "\t</agents>\n";

                            //----------------------------------  写入队列-坐席信息 ------------------------------------
                            $xml .= "\t<tiers>\n";
                            foreach ($data['queues'] as $queue){
                                if (isset($queue['agents'])&&!empty($queue['agents'])) {
                                    foreach ($queue['agents'] as $agent){
                                        $xml .= "\t\t<tier agent=\"agent".$agent['id']."\" queue=\"queue".$queue['id']."\" level=\"1\" position=\"1\"/>\n";
                                    }
                                }
                            }
                            $xml .= "\t</tiers>\n";
                            $xml .= "</configuration>\n";
                            //生成配置文件
                            file_put_contents($conf['callcenter'],$xml);
                            exec($command."\""."reload mod_callcenter"."\"");
                            $return = ['code'=>0,'msg'=>'分机更新成功'];
                            break;
                        case '/favicon.ico':
                            $response->status(404);
                            $response->end();
                            break;
                        default:
                            $return = ['code'=>1,'msg'=>'uri error'];
                    }
                    $response->end(json_encode($return));
                }catch(\Exception $e){
                    print_r($e->getMessage());
                    $response->end(json_encode(['code'=>1,'msg'=>'error','data'=>$e->getMessage()]));
                }
            }else{
                $response->end("hello word!");
            }
        });
        $http->start();
    }
}
