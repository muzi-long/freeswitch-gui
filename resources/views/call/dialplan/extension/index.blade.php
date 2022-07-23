@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                @can('call.extension.destroy')
                <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete" data-url="{{ route('call.extension.destroy') }}">删除</button>
                @endcan
                @can('call.extension.create')
                <a class="layui-btn layui-btn-sm" id="addBtn" >添加</a>
                @endcan
                @can('call.extension.updateXml')
                <button type="button" class="layui-btn layui-btn-sm" id="updateXml">更新配置</button>
                @endcan
            </div>

        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('call.extension.show')
                    <a class="layui-btn layui-btn-sm" lay-event="show">详情</a>
                    @endcan
                    @can('call.extension.edit')
                    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    <a class="layui-btn layui-btn-sm" lay-event="condition">拨号规则</a>
                    @can('call.extension.destroy')
                    <a class="layui-btn layui-btn-danger layui-btn-sm " lay-event="del">删除</a>
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
                ,height: 500
                ,url: "{{ route('call.extension') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'display_name', title: '名称'}
                    ,{field: 'name', title: '标识符'}
                    ,{field: 'context_name', title: '类型'}
                    ,{field: 'continue', title: 'continue'}
                    ,{field: 'sort', title: '序号',width:80}
                    ,{field: 'created_at', title: '添加时间',width:170}
                    ,{fixed: 'right', width: 260, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'del'){
                    deleteData(obj,"{{ route('call.extension.destroy') }}");
                } else if(layEvent === 'edit'){
                    layer.open({
                        type: 2,
                        title: "编辑",
                        shadeClose: true,
                        area: ["800px","600px"],
                        content: '/call/extension/'+data.id+'/edit',
                        end: function () {
                            dataTable();
                        }
                    })
                } else if(layEvent === 'condition'){
                    newTab('/call/extension/'+data.id+'/condition',data.display_name+' - 拨号规则');
                } else if(layEvent === 'show'){
                    newTab('/call/extension/'+data.id+'/show',data.display_name+' - 详情');
                }
            });

            $("#addBtn").click(function () {
                layer.open({
                    type: 2,
                    title: "添加",
                    shadeClose: true,
                    area: ["800px","600px"],
                    content: "{{route("call.extension.create")}}",
                    end: function () {
                        dataTable();
                    }
                })
            })
            //更新配置
            $("#updateXml").click(function () {
                layer.confirm('该操作将重新拨号计划，确认操作吗？', function(index){
                    layer.close(index);
                    var load = layer.load()
                    $.post("{{ route('call.extension.updateXml') }}",{_method:'post',_token:'{{csrf_token()}}'},function (result) {
                        layer.close(load);
                        var icon = result.code==0?6:5;
                        layer.msg(result.msg,{icon:icon})
                    });
                })
            })
        })
    </script>
@endsection
