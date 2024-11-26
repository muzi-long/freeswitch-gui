@extends('base')

@section('content')
    <div class="layui-card">
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
            </div>
            </div>
        </div>
    </div>

    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <b>呼叫记录</b>
            <form class="layui-form">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">号码</label>
                        <div class="layui-input-inline">
                            <input type="text" name="phone" placeholder="请输入呼叫号码" maxlength="11" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <button type="button" lay-submit lay-filter="search" class="layui-btn layui-btn-sm">搜索</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                @{{# if(d.billsec>0 && d.record_file){ }}
                    <a class="layui-btn layui-btn-sm" lay-event="play">播放</a>
                @{{# } }}
            </script>
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
            var $ = layui.jquery;
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

            //呼叫记录
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 500
                ,url: "{{ route('callcenter.task.calls',['task_id'=>$task->id]) }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {field: 'uuid', title: '通话编号'}
                    ,{field: 'phone', title: '呼叫号码'}
                    ,{field: 'status_name', title: '呼叫状态'}
                    ,{field: 'datetime_originate_phone', title: '呼叫时间',width: 200}
                    ,{field: 'datetime_entry_queue', title: '入队列时间',width: 200}
                    ,{field: 'datetime_agent_answered', title: '坐席接通时间',width: 200}
                    ,{field: 'datetime_end', title: '结束时间',width: 200}
                    ,{field: 'sip_username', title: '接听坐席'}
                    ,{field: 'user_nickname', title: '接听人'}
                    ,{field: 'billsec', title: '通话时长（秒）'}
                    ,{fixed: 'right', width: 100, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if (layEvent === 'play'){
                    if (data.billsec>0 && data.record_file) {
                        var _html = '<div style="padding:20px;">';
                        _html += '<audio controls="controls" autoplay src="' + data.record_file + '"></audio>';
                        _html += '</div>';
                        layer.open({
                            title: '播放录音',
                            type: 1,
                            area: ['360px', 'auto'],
                            content: _html
                        })
                    }
                }
            });
        })
    </script>
@endsection
