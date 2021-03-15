@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form  class="layui-form">
                <button class="layui-btn layui-btn-sm" lay-submit lay-filter="*" >搜索</button>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">主叫号码</label>
                        <div class="layui-input-block">
                            <input type="text" name="caller" class="layui-input" placeholder="主叫号码">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">被叫号码</label>
                        <div class="layui-input-block">
                            <input type="text" name="callee" class="layui-input" placeholder="被叫号码">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">呼叫时间</label>
                        <div class="layui-input-inline" style="width: 160px">
                            <input type="text" name="start_at" id="start_at_start" class="layui-input" placeholder="开始时间">
                        </div>
                        <div class="layui-form-mid layui-word-aux">-</div>
                        <div class="layui-input-inline" style="width: 160px">
                            <input type="text" name="end_at" id="start_at_end" class="layui-input" placeholder="结束时间">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                @{{#  if(d.billsec>0 && d.record_file){ }}
                <div class="layui-btn-group">
                    <a class="layui-btn layui-btn-sm" lay-event="play">播放</a>
                </div>
                @{{# } }}
            </script>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer','table','form','laydate'],function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            var laydate = layui.laydate;
            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 'full-200'
                ,url: "{{ route('call.cdr') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    //{checkbox: true,fixed: true}
                    {field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'uuid', title: '记录编号'}
                    ,{field: 'src', title: '主叫号码',style:'color:green'}
                    ,{field: 'dst', title: '被叫号码',style:'color:#2F4056'}
                    ,{field: 'start_time', title: '呼叫时间', sort: true}
                    ,{field: 'billsec', title: '通话时长(秒)', sort: true, style:'color: green'}
                    ,{width: 220, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if (layEvent === 'play'){
                    if (data.billsec>0 && data.record_file){
                        var _html = '<div style="padding:20px;">';
                        _html += '<audio controls="controls" autoplay src="'+data.record_file+'"></audio>';
                        _html += '</div>';
                        layer.open({
                            title : '播放录音',
                            type : 1,
                            area : ['360px','auto'],
                            content : _html
                        })
                    }

                }
            });

            //时间选择
            laydate.render({type: 'datetime', elem: '#start_at_start'});
            laydate.render({type: 'datetime', elem: '#start_at_end'});


        })
    </script>
@endsection
