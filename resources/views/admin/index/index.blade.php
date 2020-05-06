@extends('admin.base')

@section('content')
    <div class="layui-row layui-col-space15">
            {{--<div class="layui-col-md12">
                <div class="layui-row layui-col-space15">
                    <div class="layui-col-md4">
                        <div class="layui-card">
                            <div class="layui-card-header">快捷方式</div>
                            <div class="layui-card-body">
                                <div class="layui-carousel layadmin-carousel layadmin-shortcut">
                                    <div >
                                        <ul class="layui-row layui-col-space10">
                                            <li class="layui-col-xs3">
                                                <a lay-href="{{route('admin.sip')}}">
                                                    <i class="layui-icon layui-icon-console"></i>
                                                    <cite>分机管理</cite>
                                                </a>
                                            </li>
                                            <li class="layui-col-xs3">
                                                <a lay-href="{{route('admin.gateway')}}">
                                                    <i class="layui-icon layui-icon-chart"></i>
                                                    <cite>网关管理</cite>
                                                </a>
                                            </li>
                                            <li class="layui-col-xs3">
                                                <a lay-href="{{route('admin.merchant')}}">
                                                    <i class="layui-icon layui-icon-template-1"></i>
                                                    <cite>商户管理</cite>
                                                </a>
                                            </li>
                                            <li class="layui-col-xs3">
                                                <a lay-href="{{route('admin.member')}}">
                                                    <i class="layui-icon layui-icon-chat"></i>
                                                    <cite>员工管理</cite>
                                                </a>
                                            </li>
                                            <li class="layui-col-xs3">
                                                <a lay-href="{{route('admin.extension')}}">
                                                    <i class="layui-icon layui-icon-find-fill"></i>
                                                    <cite>拨号计划</cite>
                                                </a>
                                            </li>
                                            <li class="layui-col-xs3">
                                                <a lay-href="{{route('admin.user')}}">
                                                    <i class="layui-icon layui-icon-survey"></i>
                                                    <cite>用户管理</cite>
                                                </a>
                                            </li>
                                            <li class="layui-col-xs3">
                                                <a lay-href="{{route('admin.role')}}">
                                                    <i class="layui-icon layui-icon-user"></i>
                                                    <cite>角色管理</cite>
                                                </a>
                                            </li>
                                            <li class="layui-col-xs3">
                                                <a lay-href="{{route('admin.user.changeMyPasswordForm')}}">
                                                    <i class="layui-icon layui-icon-set"></i>
                                                    <cite>修改密码</cite>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-card">
                            <div class="layui-card-header">服务配置</div>
                            <div class="layui-card-body">
                                <div class="layui-carousel layadmin-carousel layadmin-backlog">
                                    <div>
                                        <ul class="layui-row layui-col-space10">
                                            <li class="layui-col-xs6">
                                                <a  class="layadmin-backlog-body">
                                                    <h3>商户数量</h3>
                                                    <p><cite>{{$merchantNum}}</cite></p>
                                                </a>
                                            </li>
                                            <li class="layui-col-xs6">
                                                <a  class="layadmin-backlog-body">
                                                    <h3>员工数量</h3>
                                                    <p><cite>{{$memberNum}}</cite></p>
                                                </a>
                                            </li>
                                            <li class="layui-col-xs6">
                                                <a  class="layadmin-backlog-body">
                                                    <h3>网关数量</h3>
                                                    <p><cite>{{$gatewayNum}}</cite></p>
                                                </a>
                                            </li>
                                            <li class="layui-col-xs6">
                                                <a  class="layadmin-backlog-body">
                                                    <h3>分机数量</h3>
                                                    <p><cite>{{$sipNum}}</cite></p>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-card">
                            <div class="layui-card-header">版本信息</div>
                            <div class="layui-card-body layui-text">
                                <table class="layui-table">
                                    <colgroup>
                                        <col width="100"><col>
                                    </colgroup>
                                    <tbody>
                                    <tr>
                                        <td>当前版本</td>
                                        <td>v2.0.0</td>
                                    </tr>
                                    <tr>
                                        <td>基于系统</td>
                                        <td>freeswitch1.8+laravel6.*+LayuiAdmin</td>
                                    </tr>
                                    <tr>
                                        <td>主要特色</td>
                                        <td>外呼功能 / 批量呼出 / 通话记录 / 客户CRM</td>
                                    </tr>
                                    <tr>
                                        <td>获取渠道</td>
                                        <td style="padding-bottom: 0;">
                                            <div class="layui-btn-container">
                                                <a href="javascript:;" onclick="layer.tips('请联系作者', this, {tips: 1});" class="layui-btn layui-btn-danger">获取授权</a>
                                                <a href="/uploads/client.zip" class="layui-btn">客户端下载</a>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md12">
                        <div class="layui-card">
                            <div class="layui-card-header">呼叫统计</div>
                            <div class="layui-card-body layui-text">
                                <table class="layui-table">
                                    <colgroup>
                                        <col align="center"><col>
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <td rowspan="2"><b>商户</b></td>
                                        <td rowspan="2"><b>分机</b></td>
                                        <td align="center" colspan="3"><b>当日</b></td>
                                        <td align="center" colspan="3"><b>本周</b></td>
                                        <td align="center" colspan="3"><b>本月</b></td>
                                    </tr>
                                    <tr>
                                        <td align="center">呼出</td>
                                        <td align="center">接通</td>
                                        <td align="center">接通率</td>
                                        <td align="center">呼出</td>
                                        <td align="center">接通</td>
                                        <td align="center">接通率</td>
                                        <td align="center">呼出</td>
                                        <td align="center">接通</td>
                                        <td align="center">接通率</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($datas as $data)
                                        @foreach($data->sips as $sip)
                                        <tr>
                                            @if($loop->first)
                                            <td rowspan="{{count($data->sips)}}">{{$data->info->company_name}}</td>
                                            @endif
                                            <td>{{$sip->username}}</td>
                                            <td align="center" style="color: red">{{$sip->todayCalls}}</td>
                                            <td align="center" style="color: green">{{$sip->todaySuccessCalls}}</td>
                                            <td align="center" style="color: #0000FF">{{$sip->todayRateCalls}}%</td>
                                            <td align="center" style="color: red">{{$sip->weekCalls}}</td>
                                            <td align="center" style="color: green">{{$sip->weekSuccessCalls}}</td>
                                            <td align="center" style="color: #0000FF">{{$sip->weekRateCalls}}%</td>
                                            <td align="center" style="color: red">{{$sip->monthCalls}}</td>
                                            <td align="center" style="color: green">{{$sip->monthSuccessCalls}}</td>
                                            <td align="center" style="color: #0000FF">{{$sip->monthRateCalls}}%</td>
                                        </tr>
                                        @endforeach
                                    @empty
                                        <tr><td colspan="11" align="center">暂无数据</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>--}}
        </div>
@endsection

@section('script')
    <script>
        layui.use(['index', 'sample']);
    </script>
@endsection