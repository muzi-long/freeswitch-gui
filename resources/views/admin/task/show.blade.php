@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header">
            <b>任务信息</b>
            <a class="layui-btn layui-btn-sm layui-btn-primary" href="{{route('admin.task')}}" ><i class="layui-icon layui-icon-left"></i>返回</a>
        </div>
        <div class="layui-card-body">
            <div class="layui-form">
                <div class="layui-row">
                    <div class="layui-col-xs4">
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">任务名称：</label>
                            <div class="layui-form-mid layui-word-aux">{{$task->name}}</div>
                        </div>
                    </div>
                    <div class="layui-col-xs4">
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">执行日期：</label>
                            <div class="layui-form-mid layui-word-aux">{{$task->date}}</div>
                        </div>
                    </div>
                    <div class="layui-col-xs4">
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">执行时间：</label>
                            <div class="layui-form-mid layui-word-aux">{{$task->time}}</div>
                        </div>
                    </div>
                    <div class="layui-col-xs4">
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">网关：</label>
                            <div class="layui-form-mid layui-word-aux">{{$task->gateway_name}}</div>
                        </div>
                    </div>
                    <div class="layui-col-xs4">
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">队列：</label>
                            <div class="layui-form-mid layui-word-aux">{{$task->queue_name}}</div>
                        </div>
                    </div>
                    <div class="layui-col-xs4">
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">并发：</label>
                            <div class="layui-form-mid layui-word-aux">{{$task->max_channel}}</div>
                        </div>
                    </div>
                    <div class="layui-col-xs4">
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">呼叫总数：</label>
                            <div class="layui-form-mid layui-word-aux">{{$task->calls_count}}</div>
                        </div>
                    </div>
                    <div class="layui-col-xs4">
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">已呼叫：</label>
                            <div class="layui-form-mid layui-word-aux"><span style="color: green">{{$task->has_calls_count}}</span></div>
                        </div>
                    </div>
                    <div class="layui-col-xs4">
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">进度：</label>
                            <div class="layui-input-inline" style="padding-top:10px">
                                <div class="layui-progress layui-progress-big" lay-showPercent="true">
                                    <div class="layui-progress-bar layui-bg-black" lay-percent="{{$percent}}"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="layui-card">
        <div class="layui-card-header"><b>呼叫图表</b></div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space10">
            <div class="layui-col-xs6">
                <div id="result_pie" style="width: 100%;height: 400px"></div>
            </div>
            <div class="layui-col-xs6">
                <div class="layui-card">
                    <div class="layui-card-header"><b>呼叫结果</b></div>
                    <div class="layui-card-body">
                        <table class="layui-table" lay-skin="line" lay-size="sm">
                            <thead>
                            <tr><th>状态</th><th>数量</th><th>占比</th></tr>
                            </thead>
                            <tbody>
                            <tr><td>成功</td><td>{{$task->success_calls_count}}</td><td>{{$task->has_calls_count>0?100*round($task->success_calls_count/$task->has_calls_count,4).'%':'0.00%'}}</td></tr>
                            <tr><td>失败</td><td>{{$task->fail_calls_count}}</td><td>{{$task->has_calls_count>0?100*round($task->fail_calls_count/$task->has_calls_count,4).'%':'0.00%'}}</td></tr>
                            <tr><td>漏接</td><td>{{$task->miss_calls_count}}</td><td>{{$task->has_calls_count>0?100*round($task->miss_calls_count/$task->has_calls_count,4).'%':'0.00%'}}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{--<div class="layui-card">
                    <div class="layui-card-header"><b>坐席监控</b></div>
                    <div class="layui-card-body">
                        <table class="layui-table" lay-skin="line" lay-size="sm">
                            <thead>
                            <tr><th>坐席</th><th>分机</th><th>坐席状态</th><th>呼叫状态</th></tr>
                            </thead>
                            <tbody id="agentStatus">

                            </tbody>
                        </table>
                    </div>
                </div>--}}
            </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        layui.config({
            version: '1535898708509' //为了更新 js 缓存，可忽略
        }).extend({
            echarts: 'lib/extend/echarts' ,
            echartsTheme: 'lib/extend/echartsTheme' ,
        }).use(['layer','table','form','echarts','echartsTheme'],function () {
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            var echarts = layui.echarts;
            var echartsTheme = layui.echartsTheme;

            //呼叫结果
            var result_pie = echarts.init(document.getElementById('result_pie'),echartsTheme)
            result_pie.setOption({
                title: {text: "任务呼出情况", x: "center", textStyle: {fontSize: 14}},
                tooltip: {trigger: "item", formatter: "{a} <br/>{b} : {c} ({d}%)"},
                legend: {orient: "vertical", x: "left", data: ["成功", "失败", "漏接"]},
                series: [{
                    name: "呼出",
                    type: "pie",
                    radius: "55%",
                    center: ["50%", "50%"],
                    data: [
                        {value:{{$task->success_calls_count}},name:'成功'},
                        {value:{{$task->fail_calls_count}},name:'失败'},
                        {value:{{$task->miss_calls_count}},name:'漏接'}
                    ]
                }]
            });
            window.onresize = result_pie.resize

            /*function agentStatus() {
                $.post("{{route('admin.task.show',['id'=>$task->id])}}",{_token:"{{csrf_token()}}"},function (res) {
                    if (res.code==0){
                        var _html = '';
                        $.each(res.data,function (index,item) {
                            _html += '<tr>';
                            _html += '<td>'+item.name+'</td>';
                            _html += '<td>'+item.contact_name+'</td>';
                            _html += '<td>'+item.status_name+'</td>';
                            _html += '<td>'+item.state_name+'</td>';
                            _html += '</tr>';
                        })
                        $("#agentStatus").html(_html);
                        setTimeout(agentStatus,5000)
                    }
                })
            }
            agentStatus();*/
        })
    </script>
@endsection