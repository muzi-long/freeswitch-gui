@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div class="layui-btn-group">
                    @can('callcenter.queue.destroy')
                    <button class="layui-btn layui-btn-sm layui-btn-danger" type="button" id="listDelete" data-url="{{route('callcenter.queue.destroy')}}">删除</button>
                    @endcan
                    @can('callcenter.queue.create')
                    <button type="button" class="layui-btn layui-btn-sm" id="addBtn">添加</button>
                    @endcan
                    @can('callcenter.queue.updateXml')
                    <button class="layui-btn layui-btn-sm" type="button" id="updateXml">更新配置</button>
                    @endcan
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('callcenter.queue.edit')
                    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                </div>
            </script>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer','table','form','jquery'],function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 'full-200'
                ,url: "{{ route('callcenter.queue') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true}
                    ,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'name', title: '队列名称'}
                    ,{field: 'strategy_name', title: '振铃策略'}
                    ,{field: 'sips_count', title: '坐席数量'}
                    ,{field: 'max_wait_time', title: '超时时间（秒）'}
                    ,{field: 'created_at', title: '添加时间'}
                    ,{ width: 220, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'del'){
                    deleteData(obj,"{{ route('callcenter.queue.destroy') }}");
                } else if(layEvent === 'edit'){
                    layer.open({
                        type: 2,
                        title: "编辑",
                        shadeClose: true,
                        area: ["800px","600px"],
                        content: '/callcenter/queue/'+data.id+'/edit',
                    })
                }
            });

            $("#addBtn").click(function () {
                layer.open({
                    type: 2,
                    title: "添加",
                    shadeClose: true,
                    area: ["800px","600px"],
                    content: '/callcenter/queue/create',
                })
            })

            //更新配置
            $("#updateXml").click(function () {
                layer.confirm('确认更新配置吗？', function (index) {
                    layer.close(index);
                    var load = layer.load();
                    $.post("{{ route('callcenter.queue.updateXml') }}", {}, function (res) {
                        layer.close(load);
                        layer.msg(res.msg, {icon: res.code === 0 ? 1 : 2})
                    });
                })
            })
        })
    </script>
@endsection
