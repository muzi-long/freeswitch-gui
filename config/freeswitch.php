<?php
return [

    //网关目录
    'gateway_dir' => '/usr/local/freeswitch/etc/freeswitch/sip_profiles/external/',

    //callcenter_conf_dir
    'callcenter_dir' => '/usr/local/freeswitch/etc/freeswitch/autoload_configs/callcenter.conf.xml',

    //application
    'application' => [
        'set'       => '设置变量',
        'answer'    => '应答',
        'sleep'     => '睡眠',
        'hangup'    => '挂断',
        'record_session'    => '录音',
        'export'    => '导入变量',
        'bridge'    => '桥接呼叫',
        'echo'      => '回音',
        'park'      => '停泊',
        'transfer'  => '呼叫转移',
        'info'      => '显示信息',
        'lua'       => 'lua脚本',
        'detect_speech'=> 'detect_speech',
    ],

    //队列响铃模式
    'strategy' => [
        'ring-all'                      => '所有振铃',
        'longest-idle-agent'            => '空闲时长最长振铃',
        'round-robin'                   => '轮循振铃',
        'top-down'                      => '顺序振铃',
        'agent-with-least-talk-time'    => '通话时长最小振铃',
        'agent-with-fewest-calls'       => '接听最少振铃',
        'sequentially-by-agent-order'   => '优先级振铃',
        'random'                        => '随机振铃',
    ],
    //坐席状态
    'agent_status' => [
        'Logged Out'            => '签出',
        'Available'             => '示闲',
        'Available (On Demand)' => '示闲(通话完成后自动示忙)',
        'On Break'              => '示忙',
    ],
    //坐席呼叫状态
    'agent_state' => [
        'Idle'              => '空闲中(不会分配话务)',
        'Waiting'           => '空闲中(等待分配话务)',
        'In a queue call'   => '通话中'
    ],

];
