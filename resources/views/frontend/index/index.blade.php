@extends('backend.base')

@section('content')
    <div class="layui-row layui-col-space15">
            <div class="layui-col-md12">
                <div class="layui-row layui-col-space15">
                    <div class="layui-col-md4">
                        <div class="layui-card">
                            <div class="layui-card-header">分机信息</div>
                            <div class="layui-card-body">
                                <div class="layui-carousel layadmin-carousel layadmin-shortcut">
                                    <table class="layui-table" lay-skin="nob">
                                        <colgroup>
                                            <col width="100"><col>
                                        </colgroup>
                                        <tbody>
                                        <tr>
                                            <td align="right">分机号：</td>
                                            <td>{{$sip->username??''}}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">密码：</td>
                                            <td>{{$sip->password??''}}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">注册IP：</td>
                                            <td>{{$sip->freeswitch->external_ip??''}}</td>
                                        </tr>
                                        <tr>
                                            <td align="right">注册端口：</td>
                                            <td>{{$sip->freeswitch->internal_sip_port??''}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md4">
                        <div class="layui-card">
                            <div class="layui-card-header">公司配置</div>
                            <div class="layui-card-body">
                                <div class="layui-carousel layadmin-carousel layadmin-backlog">
                                    <div>
                                        <ul class="layui-row layui-col-space10">
                                            <li class="layui-col-xs6">
                                                <a  class="layadmin-backlog-body">
                                                    <h3>员工</h3>
                                                    <p><cite>{{$staff_num}}/{{$merchant->staff_num}}</cite></p>
                                                </a>
                                            </li>
                                            <li class="layui-col-xs6">
                                                <a  class="layadmin-backlog-body">
                                                    <h3>分机</h3>
                                                    <p><cite>{{$sip_num}}/{{$merchant->sip_num}}</cite></p>
                                                </a>
                                            </li>
                                            <li class="layui-col-xs6">
                                                <a  class="layadmin-backlog-body">
                                                    <h3>部门</h3>
                                                    <p><cite>{{$department_num}}</cite></p>
                                                </a>
                                            </li>
                                            <li class="layui-col-xs6">
                                                <a  class="layadmin-backlog-body">
                                                    <h3>客户总数</h3>
                                                    <p><cite>{{$project_num}}</cite></p>
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
                            <div class="layui-card-header">公司信息</div>
                            <div class="layui-card-body layui-text">
                                <table class="layui-table">
                                    <colgroup>
                                        <col width="100"><col>
                                    </colgroup>
                                    <tbody>
                                    <tr>
                                        <td>公司名称</td>
                                        <td>{{$merchant->company_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>联系人</td>
                                        <td>{{$merchant->contact_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>联系电话</td>
                                        <td>{{$merchant->contact_phone}}</td>
                                    </tr>
                                    <tr>
                                        <td>服务到期</td>
                                        <td style="padding-bottom: 0;">
                                            <div class="layui-btn-container">
                                                <a class="layui-btn">{{$merchant->expire_at}}</a>
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
                            <div class="layui-card-header">图表数据</div>
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
        }).use(['layer','table','form','echarts','echartsTheme','index'],function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            var echarts = layui.echarts;
            var echartsTheme = layui.echartsTheme;
            var myechart1 = echarts.init(document.getElementById('calls'), echartsTheme);
            $.post("{{route('frontend.index.node')}}", {}, function (res) {
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
            $.post("{{route('frontend.index.cdr')}}", {}, function (res) {
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
