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
            </ul>
            <video id="remoteVideo" ></video>
            <video id="localVideo" muted="muted"></video>
            <ul class="layui-nav layui-layout-right" lay-filter="layadmin-layout-right">
                <li class="layui-nav-item" lay-unselect >
                    <a href="javascript:;" id="call-status" style="width: 50px" ></a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;">
                        <cite id="sip-status">离线</cite>
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a href="javascript:;" id="regBtn" >在线</a></dd>
                        <dd><a href="javascript:;" id="unregBtn">离线</a></dd>
                    </dl>
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
<script src="/webrtc/sip-0.7.0.js"></script>
<script>
    layui.config({
        base: '/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'layer', 'jquery'], function () {
        var layer = layui.layer;
        var $ = layui.jquery;
        $(".change-password").on("click", function () {
            layer.open({
                type: 2,
                title: '修改密碼',
                shadeClose: true,
                area: ['30%', '50%'],
                content: '/change_my_password_form'
            })
        })


        var userAgentSession;
        var userAgent;
        function initUserAgent(){
            userAgent = new SIP.UA({
                uri: '1001@192.168.254.216',
                wsServers: ['wss://testcall.shupian.cn:7443'],
                authorizationUser: '1001',
                password: '1234',
                displayName: '1001',
                hackIpInContact: true,
                rtcpMuxPolicy: 'negotiate',
                hackWssInTransport: true,
                rel100: SIP.C.supported.SUPPORTED,
                log: {
                    level: 0, //日志等级
                },
                /**
                 * sip.js生成的随机contact字符串 设置后会以此为后缀 可以搭配 cusContactName使用,根据自身业务进行使用
                 *  设置前: sofia/internal/sip:admin10@df7jal23ls0d.invalid;
                 *  设置后: sofia/internal/sip:admin10@192.168.0.253;
                 *
                 *
                 hackIpInContact: "106.13.223.129",
                 userAgentString: "myAwesomeApp",
                 registerOptions: {
                    expires: 300,
                    registrar: 'sip:xdwh.dgg.net',
                },
                 **
                 * 此处是笔者自定义的参数,因为注册到fs时,sip.js会随机生成contact字符串,如:sofia/internal/sip:admin10@df7jal23ls0d.invalid;
                 *  笔者自己添加了一个参数,对sip.js的源码进行了修改 ,修改后效果  sofia/internal/sip:1012@192.168.0.253 不需要的可以不理会此参数
                 * 此处纠正问题,官方提供了一个参数[contactName],使用此参数即可,sip.js使用官方的即可
                 * contactName:"1012"
                 */
                contactName:"1001"
            });
            //注册成功
            userAgent.on('registered', function (e) {
                $('#sip-status').text($("#regBtn").text());
            });
            //未注册成功
            userAgent.on('unregistered', function () {
                $('#sip-status').text($("#unregBtn").text());
                $('#sip-status').click(function () {
                    return;
                })
            });
            //监听来电
            userAgent.on('invite', function (session) {
                userAgentSession = session;
                userAgentSession.on('terminated', function (message, cause) {//结束
                    $("#call-status").html("")
                    layer.msg("通话结束");
                });
                userAgentSession.accept({
                    media: {
                        render: {
                            remote: document.getElementById('remoteVideo'),
                            local: document.getElementById('localVideo')
                        },
                        constraints: {
                            audio: true,
                            video: false
                        }
                    }
                });
                $("#call-status").html('<span class="layui-badge">挂断</span>')
                $("#call-status span").click(function () {
                    if(userAgent){
                        userAgentSession.terminate()
                    }
                })
            });
            userAgent.start()
        }


        $("#regBtn").click(function () {
            initUserAgent();
            userAgent.register();
        })
        $("#unregBtn").click(function () {
            if(userAgent){
                userAgent.unregister()
            }
        })

    });
</script>

</body>
</html>
