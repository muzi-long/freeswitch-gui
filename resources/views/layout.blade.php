<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/layuiadmin/style/admin.css" media="all">
</head>
<body class="layui-layout-body">
<div id="LAY_app">
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <!-- 头部区域 -->
            <ul class="layui-nav layui-layout-left">
                <li class="layui-nav-item layadmin-flexible" lay-unselect>
                    <a href="javascript:;" layadmin-event="flexible" title="侧边伸缩">
                        <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible"></i>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a>
                        <i class="layui-icon layui-icon-website"></i>
                    </a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;" layadmin-event="refresh" title="刷新">
                        <i class="layui-icon layui-icon-refresh-3"></i>
                    </a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;" >
                        <video id="myVideo" autoplay style="display: none" ></video>
                    </a>
                </li>
            </ul>

            <ul class="layui-nav layui-layout-right" lay-filter="layadmin-layout-right">
                <li class="layui-nav-item" lay-unselect >
                    <a href="javascript:;" id="callStatus" style="width: 50px" ></a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;">
                        <cite id="regStatus">
                            @if($data['sip_id'])
                                外呼号：{{$data['username']}}
                            @else
                                无外呼号
                            @endif
                        </cite>
                    </a>
                    @if($data['sip_id'])
                    <dl class="layui-nav-child">
                        <dd><a href="javascript:;" id="regBtn" >在线</a></dd>
                        <dd><a href="javascript:;" id="unregBtn">离线</a></dd>
                    </dl>
                    @endif
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a layadmin-event="message" lay-text="消息中心">
                        <i class="layui-icon layui-icon-notice"></i>
                        <!-- 如果有新消息，则显示小圆点 -->
                        <span class="layui-badge-dot"></span>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="javascript:;" layadmin-event="theme">
                        <i class="layui-icon layui-icon-theme"></i>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="javascript:;" layadmin-event="note">
                        <i class="layui-icon layui-icon-note"></i>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="javascript:;" layadmin-event="fullscreen">
                        <i class="layui-icon layui-icon-screen-full"></i>
                    </a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;">
                        <cite>{{auth()->user()->nickname}}</cite>
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a href="javascript:;" class="change-password">修改密码</a></dd>
                        <dd><a href="{{route("auth.logout")}}">退出</a></dd>
                    </dl>
                </li>

                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="javascript:;"><i class="layui-icon layui-icon-more-vertical"></i></a>
                </li>
                <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-unselect>
                    <a href="javascript:;" layadmin-event="more"><i class="layui-icon layui-icon-more-vertical"></i></a>
                </li>
            </ul>
        </div>

        <!-- 侧边菜单 -->
        <div class="layui-side layui-side-menu">
            <div class="layui-side-scroll">
                <div class="layui-logo" lay-href="{{route("index.console")}}">
                    <span>后台系统</span>
                </div>
                <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu"
                    lay-filter="layadmin-system-side-menu">
                    <li data-name="home" class="layui-nav-item layui-nav-itemed">
                        <a href="javascript:;" lay-tips="主页" lay-direction="2">
                            <i class="layui-icon layui-icon-home"></i>
                            <cite>主页</cite>
                        </a>
                        <dl class="layui-nav-child">
                            <dd data-name="console">
                                <i class="layui-icon layui-icon-layouts"></i>
                                <a lay-href="{{route("index.console")}}">控制台</a>
                            </dd>
                            <dd data-name="console">
                                <i class="layui-icon layui-icon-layouts"></i>
                                <a lay-href="{{route("index.onlinecall")}}">在线拨号</a>
                            </dd>
                        </dl>
                    </li>
                    @foreach(\Illuminate\Support\Facades\Session::get('menus') as $menu1)
                        <li data-name="{{$menu1['name']}}" class="layui-nav-item">
                            <a
                                @if($menu1['type']==1 && ($menu1['route'] || $menu1['url']))
                                lay-href="{{$menu1['url']?$menu1['url']:route($menu1['route'],[],false)}}"
                                @else
                                href="javascript:;"
                                @endif
                                lay-tips="{{$menu1['name']}}" lay-direction="2">
                                <i class="layui-icon {{$menu1['icon']}}"></i>
                                <cite>{{$menu1['name']}}</cite>
                            </a>
                            @if(isset($menu1['childs']) && !empty($menu1['childs']))
                                <dl class="layui-nav-child">
                                    @foreach($menu1['childs'] as $menu2)
                                        <dd data-name="{{$menu2['name']}}" >
                                            <a
                                                @if($menu2['type']==1 && ($menu2['route'] || $menu2['url']))
                                                lay-href="{{$menu2['url']?$menu2['url']:route($menu2['route'],[],false)}}"
                                                @else
                                                href="javascript:;"
                                                @endif
                                                lay-tips="{{$menu2['name']}}" lay-direction="2">
                                                <i class="layui-icon {{$menu2['icon']}}"></i>
                                                <cite>{{$menu2['name']}}</cite>
                                            </a>
                                            @if(isset($menu2['childs']) && !empty($menu2['childs']))
                                                <dl class="layui-nav-child">
                                                    @foreach($menu2['childs'] as $menu3)
                                                        <dd data-name="{{$menu3['name']}}">
                                                            <a
                                                                @if($menu3['type']==1 && ($menu3['route'] || $menu3['url']))
                                                                lay-href="{{$menu3['url']?$menu3['url']:route($menu3['route'],[],false)}}"
                                                                @else
                                                                href="javascript:;"
                                                                @endif
                                                                lay-tips="{{$menu3['name']}}" lay-direction="2">
                                                                <i class="layui-icon {{$menu3['icon']}}"></i>
                                                                <cite>{{$menu3['name']}}</cite>
                                                            </a>
                                                        </dd>
                                                    @endforeach
                                                </dl>
                                            @endif
                                        </dd>
                                    @endforeach
                                </dl>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- 页面标签 -->
        <div class="layadmin-pagetabs" id="LAY_app_tabs">
            <div class="layui-icon layadmin-tabs-control layui-icon-prev" layadmin-event="leftPage"></div>
            <div class="layui-icon layadmin-tabs-control layui-icon-next" layadmin-event="rightPage"></div>
            <div class="layui-icon layadmin-tabs-control layui-icon-down">
                <ul class="layui-nav layadmin-tabs-select" lay-filter="layadmin-pagetabs-nav">
                    <li class="layui-nav-item" lay-unselect>
                        <a href="javascript:;"></a>
                        <dl class="layui-nav-child layui-anim-fadein">
                            <dd layadmin-event="closeThisTabs"><a href="javascript:;">关闭当前标签页</a></dd>
                            <dd layadmin-event="closeOtherTabs"><a href="javascript:;">关闭其它标签页</a></dd>
                            <dd layadmin-event="closeAllTabs"><a href="javascript:;">关闭全部标签页</a></dd>
                        </dl>
                    </li>
                </ul>
            </div>
            <div class="layui-tab" lay-unauto lay-allowClose="true" lay-filter="layadmin-layout-tabs">
                <ul class="layui-tab-title" id="LAY_app_tabsheader">
                    <li lay-id="{{route("index.console")}}" lay-attr="{{route("index.console")}}" class=""><i class="layui-icon layui-icon-home"></i></li>
                </ul>
            </div>
        </div>

        <!-- 主体内容 -->
        <div class="layui-body" id="LAY_app_body">
            <div class="layadmin-tabsbody-item layui-show">
                <iframe src="{{route("index.console")}}" frameborder="0" class="layadmin-iframe"></iframe>
            </div>
        </div>

        <!-- 辅助元素，一般用于移动设备下遮罩 -->
        <div class="layadmin-body-shade" layadmin-event="shade"></div>
    </div>
</div>

<script src="/layuiadmin/layui/layui.js"></script>
<script src="/webrtc/jssip-3.7.4.min.js"></script>
<script>
    layui.config({
        base: '/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index',
        notice: 'notice/notice',
    }).use(['index', 'layer', 'jquery','notice'], function () {
        var layer = layui.layer;
        var $ = layui.jquery;
        var notice = layui.notice;
        // 初始化配置，同一样式只需要配置一次，非必须初始化，有默认配置
        notice.options = {
            closeButton:true,//显示关闭按钮
            debug:false,//启用debug
            positionClass:"toast-bottom-right",//弹出的位置,
            showDuration:"300",//显示的时间
            hideDuration:"1000",//消失的时间
            timeOut:"5000",//停留的时间
            extendedTimeOut:"1000",//控制时间
            showEasing:"swing",//显示时的动画缓冲方式
            hideEasing:"linear",//消失时的动画缓冲方式
            iconClass: 'toast-info', // 自定义图标，有内置，如不需要则传空 支持layui内置图标/自定义iconfont类名
            onclick: null, // 点击关闭回调
        };

        $(".change-password").on("click", function () {
            layer.open({
                type: 2,
                title: '修改密碼',
                shadeClose: true,
                area: ['30%', '50%'],
                content: '/change_my_password_form'
            })
        })

        @if(isset($data['sip_id'])&&$data['sip_id'])
        //获取浏览器麦克风权限
        navigator.mediaDevices.getUserMedia({audio: true, video: true});
        var currentSession = null
        var userAgent
        var conf = {
            "wss": '{{$data["wss_url"]}}:7443',
            "uri": '{{$data["uri"]}}',
            "username": '{{$data["username"]}}',
            "password": '{{$data["password"]}}',
        }
        $("#regBtn").click(function () {
            var socket = new JsSIP.WebSocketInterface('wss://' + conf.wss);
            var configuration = {
                sockets: [socket],
                uri: 'sip:' + conf.uri,
                password: conf.password
            };
            userAgent = new JsSIP.UA(configuration);

            // websocket
            userAgent.on('connected', function (e) {
                console.log('jssip websocket已连接')
            });
            userAgent.on('disconnected', function (e) {
                console.log('jssip websocket已断开')
            });

            // 来电监听
            userAgent.on('newRTCSession', function (data) {
                // 服务器呼叫该分机
                if (data.originator === 'remote') {
                    layer.msg('有新的来电，系统为您自动接听')
                    currentSession = data.session
                    currentSession.answer({
                        'mediaConstraints': {'audio': true, 'video': true}
                    })
                    //Fired when the call is accepted (2XX received/sent).
                    currentSession.on('accepted', function () {
                        console.log('已接听')
                        $("#callStatus").text('通话中')
                    })
                    //Fired when the call is confirmed (ACK received/sent).
                    currentSession.on('confirmed', function () {
                        console.log('ACK确认')
                        const stream = new MediaStream();
                        const receivers = currentSession.connection.getReceivers();
                        if (receivers) {
                            receivers.forEach(function (receiver,index) {
                                stream.addTrack(receiver.track)
                            })
                        }
                        document.getElementById("myVideo").srcObject = stream;
                        //添加挂机
                        $("#callStatus").html('<span class="layui-badge">挂断</span>')
                        $("#callStatus span").click(function () {
                            currentSession.terminate()
                        })
                    })
                    //Fired when an established call ends.
                    currentSession.on('ended', function () {
                        console.log('呼叫结束')
                        $("#callStatus").text('通话结束')
                        currentSession = null
                    })
                    //Fired when the session was unable to establish.
                    currentSession.on('failed', function () {
                        console.log('通话失败')
                        $("#callStatus").text('通话失败')
                        currentSession = null
                    })

                }
            });

            // 注册信息
            userAgent.on('registered', function (e) {
                $("#regStatus").text('在线')
            });
            userAgent.on('unregistered', function (e) {
                $("#regStatus").text('离线')
            });
            userAgent.on('registrationFailed', function (e) {
                $("#regStatus").text('注册失败')
                console.log(e)
            });
            userAgent.start()
        })

        $("#unregBtn").click(function () {
            if (userAgent !== undefined) {
                userAgent.unregister({
                    all: true
                })
            }
        })
        @endif

        const ws = new WebSocket("wss://{{$data['websocket_url']}}/wss?user_id={{auth()->user()->id}}")
        var ticker
        ws.onopen = function () {
            ticker = setInterval(function () {
                ws.send('{"scene":"heartbeat","data":""}')
            },30000)
        }
        ws.onmessage = function (e) {
            console.log("收到服务端消息：" + e.data)
            let data = JSON.parse(e.data);
            if (data.scene != undefined){
                switch (data.scene) {
                    case 'heartbeat':
                        console.log(data.data)
                        break;
                    case 'msg':
                        notice.info(data.data)
                        break;
                    default:
                        break;
                }
            }
        }
        ws.onclose = function (e) {
            console.log("websocket已断开")
            if(ticker){
                clearInterval(ticker)
            }
        }



    });
</script>

</body>
</html>
