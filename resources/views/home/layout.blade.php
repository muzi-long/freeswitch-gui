<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>小顶外呼2.0</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/admin/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/admin/layuiadmin/style/admin.css" media="all">
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
            <ul class="layui-nav layui-layout-right" lay-filter="layadmin-layout-right">
                <li class="layui-nav-item" lay-unselect>
                    <a  layadmin-event="message" lay-text="消息中心">
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
                        <cite>{{auth('merchant')->user()->contact_name}}</cite>
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a lay-href="{{route('home.user.changeMyPasswordForm')}}">修改密码</a></dd>
                        <dd><a href="{{route('home.user.logout')}}">退出</a></dd>
                    </dl>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="javascript:;" layadmin-event="about"><i class="layui-icon layui-icon-more-vertical"></i></a>
                </li>
                <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-unselect>
                    <a href="javascript:;" layadmin-event="more"><i class="layui-icon layui-icon-more-vertical"></i></a>
                </li>
            </ul>
        </div>

        <!-- 侧边菜单 -->
        <div class="layui-side layui-side-menu">
            <div class="layui-side-scroll">
                <div class="layui-logo" lay-href="{{route('home.onlinecall')}}">
                    <span>小顶外呼2.0</span>
                </div>
                <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
                    <li data-name="home" class="layui-nav-item layui-nav-itemed">
                        <a href="javascript:;" lay-tips="主页" lay-direction="2">
                            <i class="layui-icon layui-icon-home"></i>
                            <cite>主页</cite>
                        </a>
                        <dl class="layui-nav-child">
                            <dd data-name="console">
                                <a lay-href="{{route('home.onlinecall')}}">在线拨号</a>
                            </dd>
                        </dl>
                    </li>
                    <li data-name="crm" class="layui-nav-item">
                        <a href="javascript:;" lay-tips="系统管理" lay-direction="2">
                            <i class="layui-icon layui-icon-set"></i>
                            <cite>系统管理</cite>
                        </a>
                        <dl class="layui-nav-child">
                            <dd data-name="home.member" >
                                <a lay-href="{{route('home.member')}}" lay-tips="员工管理" lay-direction="2">员工管理</a>
                            </dd>
                            <dd data-name="home.sip" >
                                <a lay-href="{{route('home.sip')}}" lay-tips="分机管理" lay-direction="2">分机管理</a>
                            </dd>
                        </dl>
                    </li>
                    <li data-name="crm" class="layui-nav-item">
                        <a href="javascript:;" lay-tips="CRM管理" lay-direction="2">
                            <i class="layui-icon layui-icon-group"></i>
                            <cite>CRM管理</cite>
                        </a>
                        <dl class="layui-nav-child">
                            <dd data-name="home.node" >
                                <a lay-href="{{route('home.node')}}" lay-tips="节点管理" lay-direction="2">节点管理</a>
                            </dd>
                            <dd data-name="home.node" >
                                <a lay-href="{{route('home.project-design')}}" lay-tips="项目管理" lay-direction="2">表单设计</a>
                            </dd>
                            <dd data-name="home.node" >
                                <a lay-href="{{route('home.project')}}" lay-tips="项目管理" lay-direction="2">项目管理</a>
                            </dd>
                            <dd data-name="home.node" >
                                <a lay-href="{{route('home.remind')}}" lay-tips="跟进提醒" lay-direction="2">跟进提醒</a>
                            </dd>
                            <dd data-name="home.node" >
                                <a lay-href="{{route('home.waste')}}" lay-tips="回收站" lay-direction="2">回收站</a>
                            </dd>
                        </dl>
                    </li>
                    <li data-name="data" class="layui-nav-item">
                        <a href="javascript:;" lay-tips="数据报告" lay-direction="2">
                            <i class="layui-icon layui-icon-group"></i>
                            <cite>数据监控</cite>
                        </a>
                        <dl class="layui-nav-child">
                            <dd data-name="home.node" >
                                <a lay-href="{{route('home.sip.count')}}" lay-tips="分机统计" lay-direction="2">分机统计</a>
                            </dd>
                            <dd data-name="home.node" >
                                <a lay-href="{{route('home.cdr')}}" lay-tips="通话记录" lay-direction="2">通话记录</a>
                            </dd>
                        </dl>
                    </li>
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
                    <li lay-id="{{route('home.onlinecall')}}" lay-attr="{{route('home.onlinecall')}}" class="layui-this"><i class="layui-icon layui-icon-home"></i></li>
                </ul>
            </div>
        </div>

        <!-- 主体内容 -->
        <div class="layui-body" id="LAY_app_body">
            <div class="layadmin-tabsbody-item layui-show">
                <iframe src="{{route('home.onlinecall')}}" frameborder="0" class="layadmin-iframe"></iframe>
            </div>
        </div>

        <!-- 辅助元素，一般用于移动设备下遮罩 -->
        <div class="layadmin-body-shade" layadmin-event="shade"></div>
    </div>
</div>

<script src="/static/admin/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/static/admin/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use('index');
</script>
</body>
</html>


