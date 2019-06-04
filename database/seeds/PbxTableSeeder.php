<?php

use Illuminate\Database\Seeder;

class PbxTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //清空表
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\Sip::truncate();
        \App\Models\Action::truncate();
        \App\Models\Condition::truncate();
        \App\Models\Extension::truncate();
        \App\Models\Group::truncate();
        \App\Models\Queue::truncate();
        \App\Models\Agent::truncate();
        \App\Models\Gateway::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        //分机用户
        for ($sip=10000; $sip<=10020; $sip++){
            \App\Models\Sip::create([
                'username'  => $sip,
                'password'  => $sip,
                'effective_caller_id_name'  => $sip,
                'effective_caller_id_number'  => $sip,
            ]);
        }

        //拨号计划
        $data = [
            [
                'display_name'  => '本地分机互拨',
                'name'          => 'Local_Extension',
                'context'       => 'default',
                'sort'          => 0,
                'conditions'    => [
                    [
                        'display_name'  => '规则一',
                        'field'         => 'destination_number',
                        'expression'    => '^(\d{5,9})$',
                        'break'         => 'on-false',
                        'sort'          => 0,
                        'actions'       => [
                            [
                                'display_name'  => '系统应答',
                                'application'   => 'answer',
                                'data'          => null,
                                'sort'          => 0,
                            ],
                            [
                                'display_name'  => '媒体绕过',
                                'application'   => 'set',
                                'data'          => 'bypass_media=true',
                                'sort'          => 1,
                            ],
                            [
                                'display_name'  => '被叫挂断后主叫也挂断',
                                'application'   => 'set',
                                'data'          => 'hangup_after_bridge=true',
                                'sort'          => 2,
                            ],
                            [
                                'display_name'  => '呼叫',
                                'application'   => 'bridge',
                                'data'          => 'user/$1',
                                'sort'          => 3,
                            ],
                        ]
                    ],
                ],
            ],
            [
                'display_name'  => '回音测试',
                'name'          => 'echo_test',
                'context'       => 'default',
                'sort'          => 1,
                'conditions'    => [
                    [
                        'display_name'  => '规则一',
                        'field'         => 'destination_number',
                        'expression'    => '^9996$',
                        'break'         => 'on-false',
                        'sort'          => 0,
                        'actions'       => [
                            [
                                'display_name'  => '系统应答',
                                'application'   => 'answer',
                                'data'          => null,
                                'sort'          => 0,
                            ],
                            [
                                'display_name'  => '回音',
                                'application'   => 'echo',
                                'data'          => null,
                                'sort'          => 1,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'display_name'  => '控制台显示info',
                'name'          => 'info_test',
                'context'       => 'default',
                'sort'          => 1,
                'conditions'    => [
                    [
                        'display_name'  => '规则一',
                        'field'         => 'destination_number',
                        'expression'    => '^9992$',
                        'break'         => 'on-false',
                        'sort'          => 0,
                        'actions'       => [
                            [
                                'display_name'  => '系统应答',
                                'application'   => 'answer',
                                'data'          => null,
                                'sort'          => 0,
                            ],
                            [
                                'display_name'  => '输出信息',
                                'application'   => 'info',
                                'data'          => null,
                                'sort'          => 1,
                            ],
                            [
                                'display_name'  => '挂断',
                                'application'   => 'hangup',
                                'data'          => null,
                                'sort'          => 2,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'display_name'  => '内线拨打外线电话',
                'name'          => 'internal_to_external',
                'context'       => 'default',
                'sort'          => 1,
                'conditions'    => [
                    [
                        'display_name'  => '规则一',
                        'field'         => 'destination_number',
                        'expression'    => '^(\d{11})$',
                        'break'         => 'on-false',
                        'sort'          => 0,
                        'actions'       => [
                            [
                                'display_name'  => '系统应答',
                                'application'   => 'answer',
                                'data'          => null,
                                'sort'          => 0,
                            ],
                            [
                                'display_name'  => '主叫随被叫一起挂断',
                                'application'   => 'set',
                                'data'          => 'hangup_after_bridge=true',
                                'sort'          => 1,
                            ],
                            [
                                'display_name'  => '设置录音文件',
                                'application'   => 'set',
                                'data'          => 'sofia_record_file=$${base_dir}/var/lib/freeswitch/recordings/${strftime(%Y)}/${strftime(%m)}/${strftime(%d)}/${strftime(%Y-%m-%d-%H-%M-%S)}_${destination_number}_${caller_id_number}.wav',
                                'sort'          => 2,
                            ],
                            [
                                'display_name'  => '接通后才进行录音',
                                'application'   => 'set',
                                'data'          => 'media_bug_answer_req=true',
                                'sort'          => 3,
                            ],
                            [
                                'display_name'  => '最小录音时间',
                                'application'   => 'set',
                                'data'          => 'RECORD_MIN_SEC=3',
                                'sort'          => 4,
                            ],
                            [
                                'display_name'  => '立体声',
                                'application'   => 'set',
                                'data'          => 'RECORD_STEREO=true',
                                'sort'          => 5,
                            ],
                            [
                                'display_name'  => '录音',
                                'application'   => 'record_session',
                                'data'          => '${sofia_record_file}',
                                'sort'          => 6,
                            ],
                            [
                                'display_name'  => '呼叫',
                                'application'   => 'bridge',
                                'data'          => '{outbound_caller_id=900013}sofia/gateway/900013/$1',
                                'sort'          => 7,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        foreach ($data as $d1){
            $extension = \App\Models\Extension::create([
                'display_name'  => $d1['display_name'],
                'name'          => $d1['name'],
                'context'       => $d1['context'],
                'sort'          => $d1['sort'],
            ]);
            if (isset($d1['conditions'])&&!empty($d1['conditions'])){
                foreach ($d1['conditions'] as $d2){
                    $condition = \App\Models\Condition::create([
                        'display_name'  => $d2['display_name'],
                        'field'         => $d2['field'],
                        'expression'    => $d2['expression'],
                        'break'         => $d2['break'],
                        'sort'          => $d2['sort'],
                        'extension_id'  => $extension->id,
                    ]);
                    if (isset($d2['actions'])&&!empty($d2['actions'])){
                        foreach ($d2['actions'] as $d3){
                            \App\Models\Action::create([
                                'display_name'  => $d3['display_name'],
                                'application'   => $d3['application'],
                                'data'          => $d3['data'],
                                'sort'          => $d3['sort'],
                                'condition_id'  => $condition->id,
                            ]);
                        }
                    }
                }
            }
        }

        //添加分机组
        for ($i=1; $i<20; $i++){
            \App\Models\Group::create([
                'display_name'  => 'group'.$i,
                'name'          => 6000+$i,
            ]);
        }

        //添加队列
        for ($i=1; $i<20; $i++){
            \App\Models\Queue::create([
                'display_name'  => 'queue'.$i,
                'name'          => 8000+$i,
            ]);
        }

        //添加坐席
        for ($i=1; $i<20; $i++){
            \App\Models\Agent::create([
                'display_name'      => str_random(8),
                'name'              => 7000+$i,
                'originate_number'  => 10000+$i,
            ]);
        }

        //添加网
        $datas = [
            [
                'name'                  => '网关一',
                'realm'                 => '125.69.90.72',
                'username'              => 'KL20190429',
                'password'              => 'VTLZMBkY',
                'outbound_caller_id'    => '600011',
            ],
            [
                'name'                  => '900011',
                'realm'                 => '120.77.227.133',
                'username'              => '900011',
                'password'              => '900011',
                'outbound_caller_id'    => '900011',
            ],
            [
                'name'                  => 'test',
                'realm'                 => '192.168.254.100',
                'username'              => 'test',
                'password'              => 'test',
            ]
        ];
        foreach ($datas as $data){
            \App\Models\Gateway::create($data);
        }

    }
}
