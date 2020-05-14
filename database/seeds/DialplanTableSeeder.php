<?php

use Illuminate\Database\Seeder;

class DialplanTableSeeder extends Seeder
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
        \App\Models\Action::truncate();
        \App\Models\Condition::truncate();
        \App\Models\Extension::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        //拨号计划
        $data = [
            [
                'display_name'  => '挂断',
                'name'          => 'hangup',
                'context'       => 'default',
                'sort'          => 0,
                'conditions'    => [
                    [
                        'display_name'  => '规则一',
                        'field'         => 'destination_number',
                        'expression'    => '^hangup$',
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
                                'display_name'  => '挂断',
                                'application'   => 'hangup',
                                'data'          => '',
                                'sort'          => 1,
                            ],
                        ]
                    ],
                ],
            ],
            [
                'display_name'  => '本地分机互拨',
                'name'          => 'Local_Extension',
                'context'       => 'default',
                'sort'          => 0,
                'conditions'    => [
                    [
                        'display_name'  => '规则一',
                        'field'         => 'destination_number',
                        'expression'    => '^(\d{4,5})$',
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
                                'data'          => 'bypass_media=false',
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
                                'sort'          => 4,
                            ],
                        ]
                    ],
                ],
            ],
            [
                'display_name'  => '内线拨打外线电话',
                'name'          => 'dial_from_gateway_to_phone',
                'context'       => 'default',
                'sort'          => 1,
                'conditions'    => [
                    [
                        'display_name'  => '规则一',
                        'field'         => 'destination_number',
                        'expression'    => '^(gw\d+)_(\d{11,20})_([a-z0-9]{16,32})$',
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
                                'display_name'  => '设置网关',
                                'application'   => 'set',
                                'data'          => 'gw=$1',
                                'sort'          => 1,
                            ],
                            [
                                'display_name'  => '设置被叫号码',
                                'application'   => 'set',
                                'data'          => 'dialed_extension=$2',
                                'sort'          => 2,
                            ],
                            [
                                'display_name'  => '设置uuid到bleg',
                                'application'   => 'export',
                                'data'          => 'nolocal:origination_uuid=$3',
                                'sort'          => 3,
                            ],
                            [
                                'display_name'  => '主叫随被叫一起挂断',
                                'application'   => 'set',
                                'data'          => 'hangup_after_bridge=true',
                                'sort'          => 5,
                            ],
                            [
                                'display_name'  => '呼叫失败时继续来播放提示音',
                                'application'   => 'set',
                                'data'          => 'continue_on_fail=true',
                                'sort'          => 6,
                            ],
                            [
                                'display_name'  => '呼叫',
                                'application'   => 'bridge',
                                'data'          => 'sofia/gateway/${gw}/${dialed_extension}',
                                'sort'          => 11,
                            ],
                            [
                                'display_name'  => '系统应答',
                                'application'   => 'answer',
                                'data'          => null,
                                'sort'          => 12,
                            ],
                            [
                                'display_name'  => '等待1秒',
                                'application'   => 'sleep',
                                'data'          => '1000',
                                'sort'          => 13,
                            ],
                            [
                                'display_name'  => '播放提示',
                                'application'   => 'playback',
                                'data'          => '/usr/local/freeswitch/sounds/${originate_disposition}.wav',
                                'sort'          => 14,
                            ],
                            [
                                'display_name'  => '挂机',
                                'application'   => 'hangup',
                                'data'          => null,
                                'sort'          => 15,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'display_name'  => '客户端直接拨号',
                'name'          => 'client_to_phone',
                'context'       => 'default',
                'sort'          => 1,
                'conditions'    => [
                    [
                        'display_name'  => '规则一',
                        'field'         => 'destination_number',
                        'expression'    => '^(\d{6,11})$',
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
                                'display_name'  => '等待1秒',
                                'application'   => 'sleep',
                                'data'          => '1000',
                                'sort'          => 2,
                            ],
                            [
                                'display_name'  => '播放提示',
                                'application'   => 'playback',
                                'data'          => '/usr/local/freeswitch/sounds/client_to_phone.wav',
                                'sort'          => 3,
                            ],
                            [
                                'display_name'  => '挂断',
                                'application'   => 'hangup',
                                'data'          => null,
                                'sort'          => 4,
                            ],
                        ],
                    ],
                ],
            ],
            //呼入
            [
                'display_name'  => '952273呼入',
                'name'          => 'call_in_952273',
                'context'       => 'public',
                'sort'          => 0,
                'conditions'    => [
                    [
                        'display_name'  => '规则一',
                        'field'         => 'destination_number',
                        'expression'    => '^(02863803994|952273|63803994)$',
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
                                'display_name'  => '等待1秒',
                                'application'   => 'sleep',
                                'data'          => '1000',
                                'sort'          => 1,
                            ],
                            [
                                'display_name'  => '呼叫失败时继续来播放提示音',
                                'application'   => 'set',
                                'data'          => 'continue_on_fail=true',
                                'sort'          => 2,
                            ],
                            [
                                'display_name'  => '转到分机',
                                'application'   => 'bridge',
                                'data'          => 'user/8971|user/8972|user/8973',
                                'sort'          => 3,
                            ],
                            [
                                'display_name'  => '系统应答',
                                'application'   => 'answer',
                                'data'          => null,
                                'sort'          => 4,
                            ],
                            [
                                'display_name'  => '播放提示',
                                'application'   => 'playback',
                                'data'          => '/usr/local/freeswitch/sounds/kefu_is_busy.wav',
                                'sort'          => 6,
                            ],
                            [
                                'display_name'  => '挂机',
                                'application'   => 'hangup',
                                'data'          => null,
                                'sort'          => 7,
                            ],
                        ]
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
    }
}
