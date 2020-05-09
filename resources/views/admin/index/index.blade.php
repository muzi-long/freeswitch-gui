@extends('admin.base')

@section('content')
    <div class="layui-row layui-col-space15">
            <div class="layui-col-md12">
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
                                                <a lay-href="{{route('admin.department')}}">
                                                    <i class="layui-icon layui-icon-template-1"></i>
                                                    <cite>部门管理</cite>
                                                </a>
                                            </li>
                                            <li class="layui-col-xs3">
                                                <a lay-href="{{route('admin.node')}}">
                                                    <i class="layui-icon layui-icon-chat"></i>
                                                    <cite>节点管理</cite>
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
                                                    <h3>部门数量</h3>
                                                    <p><cite>{{$departmentCount}}</cite></p>
                                                </a>
                                            </li>
                                            <li class="layui-col-xs6">
                                                <a  class="layadmin-backlog-body">
                                                    <h3>用户数量</h3>
                                                    <p><cite>{{$userCount}}</cite></p>
                                                </a>
                                            </li>
                                            <li class="layui-col-xs6">
                                                <a  class="layadmin-backlog-body">
                                                    <h3>总客户数</h3>
                                                    <p><cite>{{$projectCount}}</cite></p>
                                                </a>
                                            </li>
                                            <li class="layui-col-xs6">
                                                <a  class="layadmin-backlog-body">
                                                    <h3>公海客户数量</h3>
                                                    <p><cite>{{$wasteCount}}</cite></p>
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
                            <div class="layui-card-header">最近15天数据</div>
                            <div class="layui-card-body">
                                <div class="layui-row layui-col-space30">
                                    <div class="layui-col-md6">
                                        <div id="calls" style="height: 400px"></div>
                                    </div>
                                    <div class="layui-col-md6">
                                        <div id="projects" style="height: 400px"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('script')
    <script>
        layui.extend({
            echarts: 'lib/extend/echarts' ,
            echartsTheme: 'lib/extend/echartsTheme' ,
        }).use(['layer','table','form','echarts','echartsTheme'],function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            var echarts = layui.echarts;
            var echartsTheme = layui.echartsTheme;
            var myechart1 = echarts.init(document.getElementById('calls'), echartsTheme);
            $.post("{{route('admin.remind.count')}}", {}, function (res) {
                if (res.code == 0) {
                    var legend = [];
                    var series = [];
                    $.each(res.data, function (index, elem) {
                        legend.push(index);
                        series.push({
                            value: elem,
                            name: index
                        })
                    });
                    myechart1.setOption({
                        title: {text: "各节点客户分布", x: "center", textStyle: {fontSize: 14}},
                        tooltip: {trigger: "item", formatter: "{a} <br/>{b} : {c} ({d}%)"},
                        legend: {orient: "vertical", x: "left", data: legend},
                        series: [{
                            name: "客户数",
                            type: "pie",
                            radius: "55%",
                            center: ["50%", "50%"],
                            data: series
                        }]
                    });
                }
            });
            window.onresize = myechart1.resize;

            var myechart2 = echarts.init(document.getElementById('projects'), echartsTheme);
            $.post("{{route('admin.index.chart')}}", {}, function (res) {
                if (res.code == 0) {
                    var months = [];
                    var calls = [];
                    var success = [];
                    $.each(res.data.months, function (index, elem) {
                        months.push(elem);
                    });
                    $.each(res.data.calls, function (index, elem) {
                        calls.push(elem);
                    });
                    $.each(res.data.success, function (index, elem) {
                        success.push(elem);
                    });
                    myechart2.setOption({
                        title: {text: "本年度总呼叫量和接通量", subtext: ""},
                        tooltip: {trigger: "axis"},
                        legend: {data: ["总呼叫量", "接通量"]},
                        calculable: !0,
                        xAxis: [{
                            type: "category",
                            data: months
                        }],
                        yAxis: [{type: "value"}],
                        series: [{
                            name: "总呼叫量",
                            type: "bar",
                            data: calls,
                            markPoint: {data: [{type: "max", name: "最大值"}, {type: "min", name: "最小值"}]},
                            markLine: {data: [{type: "average", name: "平均值"}]}
                        }, {
                            name: "接通量",
                            type: "bar",
                            data: success,
                            markPoint: {data: [{type: "max", name: "最大值"}, {type: "min", name: "最小值"}]},
                            markLine: {data: [{type: "average", name: "平均值"}]}
                        }]
                    });
                }
            });
            window.onresize = myechart2.resize;
        })
    </script>
@endsection