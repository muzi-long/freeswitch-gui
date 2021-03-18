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
    ],

    'esl' => [
        'host' => '127.0.0.1',
        'password' => 'dgg@1234.',
        'port' => 8022,
    ],

    'redis_key' => [
        'dial' => 'dial_uuid_queue',
    ],
    'record_url' => env('APP_URL','http://localhost'),
    'host' => '192.168.254.216',
    'wss_url' => 'testcall.shupian.cn',


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

    //字段类型
    'field_type' => [
        'input' => '输入框',
        'radio' => '单选',
        'checkbox' => '多选',
        'select' => '下拉选择',
        'image' => '图片上传',
        'textarea' => '文本框',
    ],

];
