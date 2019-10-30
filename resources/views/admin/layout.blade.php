<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{session('configuration.site_title')}}</title>
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
                        <cite>{{auth()->user()->nickname ?? auth()->user()->username}}</cite>
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a lay-href="{{route('admin.user.changeMyPasswordForm')}}">修改密码</a></dd>
                        <dd><a href="{{route('admin.user.logout')}}">退出</a></dd>
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
                <div class="layui-logo" lay-href="{{route('admin.index')}}">
                    <span>{{session('configuration.site_title')}}</span>
                </div>
                <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
                    <li data-name="home" class="layui-nav-item layui-nav-itemed">
                        <a href="javascript:;" lay-tips="主页" lay-direction="2">
                            <i class="layui-icon layui-icon-home"></i>
                            <cite>主页</cite>
                        </a>
                        <dl class="layui-nav-child">
                            <dd data-name="console" class="layui-this">
                                <a lay-href="{{route('admin.index')}}">控制台</a>
                            </dd>
                        </dl>
                    </li>
                    @foreach($menus as $menu1)
                        @can($menu1->name)
                            @if($menu1->visiable==1)
                                <li data-name="{{$menu1->name}}" class="layui-nav-item">
                                    <a
                                       @if($menu1->type==2 && ($menu1->route || $menu1->url))
                                            lay-href="{{$menu1->route?route($menu1->route):$menu1->url}}"
                                       @else
                                            href="javascript:;"
                                       @endif
                                       lay-tips="{{$menu1->display_name}}" lay-direction="2">
                                        <i class="layui-icon {{$menu1->icon}}"></i>
                                        <cite>{{$menu1->display_name}}</cite>
                                    </a>
                                    @if($menu1->type==1 && $menu1->allChilds->isNotEmpty())
                                    <dl class="layui-nav-child">
                                        @foreach($menu1->allChilds as $menu2)
                                            @can($menu2->name)
                                                @if($menu2->visiable==1)
                                                    <dd data-name="{{$menu2->name}}" >
                                                        <a
                                                            @if($menu2->type==2 && ($menu2->route || $menu2->url))
                                                                lay-href="{{$menu2->route?route($menu2->route):$menu2->url}}"
                                                            @else
                                                                href="javascript:;"
                                                            @endif
                                                            lay-tips="{{$menu2->display_name}}" lay-direction="2">
                                                            <i class="layui-icon {{$menu2->icon}}"></i>
                                                            <cite>{{$menu2->display_name}}</cite>
                                                        </a>
                                                        @if($menu2->type==1 && $menu2->allChilds->isNotEmpty())
                                                            <dl class="layui-nav-child">
                                                                @foreach($menu2->allChilds as $menu3)
                                                                    @can($menu3->name)
                                                                        @if($menu3->visiable==1)
                                                                        <dd data-name="{{$menu3->name}}">
                                                                            <a
                                                                                @if($menu3->type==2 && ($menu3->route || $menu3->url))
                                                                                    lay-href="{{$menu3->route?route($menu3->route):$menu3->url}}"
                                                                                @else
                                                                                    href="javascript:;"
                                                                                @endif
                                                                                lay-tips="{{$menu3->display_name}}" lay-direction="2">
                                                                                <i class="layui-icon {{$menu3->icon}}"></i>
                                                                                <cite>{{$menu3->display_name}}</cite>
                                                                            </a>
                                                                        </dd>
                                                                        @endif
                                                                    @endcan
                                                                @endforeach
                                                            </dl>
                                                        @endif
                                                    </dd>
                                                @endif
                                            @endcan
                                        @endforeach
                                    </dl>
                                    @endif
                                </li>
                            @endif
                        @endcan
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
                    <li lay-id="{{route('admin.index')}}" lay-attr="{{route('admin.index')}}" class="layui-this"><i class="layui-icon layui-icon-home"></i></li>
                </ul>
            </div>
        </div>

        <!-- 主体内容 -->
        <div class="layui-body" id="LAY_app_body">
            <div class="layadmin-tabsbody-item layui-show">
                <iframe src="{{route('admin.index')}}" frameborder="0" class="layadmin-iframe"></iframe>
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


