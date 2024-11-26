@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete" data-url="{{ route('call.action.destroy') }}">删除</button>
                <a class="layui-btn layui-btn-sm" id="addBtn" >添 加</a>
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    <a class="layui-btn layui-btn-danger layui-btn-sm " lay-event="del">删除</a>
                </div>
            </script>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer','table','form'],function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 500
                ,url: "{{ route('call.action',['condition_id'=>$condition->id]) }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'display_name', title: '名称'}
                    ,{field: 'application_name', title: '应用'}
                    ,{field: 'data', title: '数据'}
                    ,{field: 'sort', title: '序号',width:80}
                    ,{field: 'created_at', title: '添加时间',width:170}
                    ,{fixed: 'right', width: 220, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'del'){
                    deleteData(obj,"{{ route('call.action.destroy') }}");
                } else if(layEvent === 'edit'){
                    layer.open({
                        type: 2,
                        title: "编辑",
                        shadeClose: true,
                        area: ["800px","600px"],
                        content: '/call/condition/'+data.condition_id+'/action/'+data.id+'/edit',
                        end: function () {
                            dataTable();
                        }
                    })
                }
            });

            $("#addBtn").click(function () {
                layer.open({
                    type: 2,
                    title: "添加",
                    shadeClose: true,
                    area: ["800px","600px"],
                    content: "{{route('call.action.create',['condition_id'=>$condition->id])}}",
                    end: function () {
                        dataTable();
                    }
                })
            })
        })
    </script>
@endsection
