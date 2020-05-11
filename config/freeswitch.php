<?php

return [
    //超级管理员
    'user_root_id' => 1,
    //超级角色
    'role_root_id' => 1,
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
        'log' => 'log',
    ],

    //生成静态文件地址
    'swoole_http_url' => [
        //生成网关
        'gateway' => 'http://127.0.0.1:8001/gateway',
        //生成分机
        'directory' => 'http://127.0.0.1:8001/directory',
        //生成拨号计划
        'dialplan' => 'http://127.0.0.1:8001/dialplan',
        //生成callcenter
        'callcenter' => 'http://127.0.0.1:8001/callcenter',
    ],

    'esl' => [
        'host' => '127.0.0.1',
        'password' => 'dgg@1234.',
        'port' => 8022,
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
];