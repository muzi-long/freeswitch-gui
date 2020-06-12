<?php

return [
    //超级管理员
    'user_root_id' => 1,
    //超级角色
    'role_root_id' => 1,
    //application
    'application' => [
        'set'               => '设置变量',
        'answer'            => '应答',
        'sleep'             => '睡眠',
        'hangup'            => '挂断',
        'record_session'    => '录音',
        'export'            => '导入变量',
        'bridge'            => '桥接呼叫',
        'echo'              => '回音',
        'park'              => '停泊',
        'transfer'          => '呼叫转移',
        'info'              => '显示信息',
        'lua'               => 'lua脚本',
        'log'               => 'log',
        'playback'          => '播放',
    ],

    //生成静态文件地址
    'swoole_http_url' => [
        //生成网关
        'gateway' => 'http://127.0.0.1:9501/gateway',
        //生成分机
        'directory' => 'http://127.0.0.1:9501/directory',
        //生成拨号计划
        'dialplan' => 'http://127.0.0.1:9501/dialplan',
        //生成callcenter
        'callcenter' => 'http://127.0.0.1:9501/callcenter',
    ],

    'esl' => [
        'host' => '127.0.0.1',
        'password' => 'ClueCon',
        'port' => 8021,
    ],

    //队列响铃模式
    'strategy' => [
        'top-down'                      => '顺序振铃',
        'ring-all'                      => '所有振铃',
        'longest-idle-agent'            => '空闲时长最长振铃',
        'round-robin'                   => '轮循振铃',
        'agent-with-least-talk-time'    => '通话时长最小振铃',
        'agent-with-fewest-calls'       => '接听最少振铃',
        'sequentially-by-agent-order'   => '优先级振铃',
        'random'                        => '随机振铃',
    ],

    //呼叫状态
    'channel_callstate' => [
        'DOWN' => '空闲',
        'HANGUP' => '空闲',
        'RINGING' => '响铃',
        'RING_WAIT' => '响铃',
        'EARLY' => '响铃',
        'ACTIVE' => '通话中',

    ],

    //坐席状态status
    'agent_status' => [
        'Logged Out'    => '签出',
        'Available'     => '示闲',
        'On Break'      => '休息(不接收呼叫)',
    ],
    //坐席呼叫状态state
    'agent_state' => [
        'Idle'              => '空闲（不接收呼叫）',
        'Waiting'           => '等待',
        'Receiving'         => '电话呼入',
        'In a queue call'   => '通话中',
    ],
    //群呼状态
    'callcenter_call_status' => [
        1 => '待呼叫',
        2 => '呼叫失败',
        3 => '漏接',
        4 => '成功',
    ],
    //字段类型
    'field_type' => [
        'input' => '输入框',
        'radio' => '单选',
        'checkbox' => '多选',
        'select' => '下拉选择',
        'image' => '图片上传',
        'textarea' => '文本框',
    ],
    //redis key
    'redis_key' => [
        //服务端获取任务ID的key
        'callcenter_task' => 'callcenter_task_id',
        //自增ID的key,用于群呼时生成uuid
        'callcenter_call' => 'callcenter_call_id',
    ],
    'baidu' => [
        'appId' => '15143241',
        'appKey' => 'YI8bXhDcthkBrQNRQFDf2iCG',
        'appSecret' => 'NQo5Nk9rEZVAndjthwQd8OtYvNfjBcFM',
        'url' => [
            'token' => 'https://openapi.baidu.com/oauth/2.0/token',
            'tts' => 'http://tsn.baidu.com/text2audio',
            'asr' => 'https://vop.baidu.com/pro_api',
        ],
    ],
];
