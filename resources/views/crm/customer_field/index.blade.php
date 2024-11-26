@extends('base')

@section('content')
    <style>
        .layui-table tbody .layui-table-cell{
            height:50px;
            line-height: 50px;
        }
    </style>
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                @can('crm.customer_field.create')
                <a class="layui-btn layui-btn-sm" id="addBtn" >添加</a>
                @endcan
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('crm.customer_field.edit')
                    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    @can('crm.customer_field.destroy')
                    <a class="layui-btn layui-btn-danger layui-btn-sm " lay-event="del">删除</a>
                    @endcan
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
                ,height: 'full-200'
                ,url: "{{ route('crm.customer_field') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {field: 'field_label', title: '字段名称'}
                    ,{field: 'field_key', title: '字段Key'}
                    ,{field: 'field_type_name', title: '字段类型'}
                    ,{field: 'field_option', title: '字段配置项'}
                    ,{field: 'field_value', title: '默认值'}
                    ,{field: 'sort', title: '排序'}
                    ,{field: 'visiable', title: '可见性',templet:function (d) {
                            return d.visiable==1?'显示':'隐藏';
                        }}
                    ,{field: 'visiable', title: '是否必填',templet:function (d) {
                            return d.required==1?'是':'否';
                        }}
                    ,{field: 'created_at', title: '创建时间'}
                    ,{align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'del'){
                    deleteData(obj,"{{ route('crm.customer_field.destroy') }}");
                } else if(layEvent === 'edit'){
                    layer.open({
                        type: 2,
                        title: "编辑",
                        shadeClose: true,
                        area: ["800px","600px"],
                        content: '/crm/customer_field/'+data.id+'/edit',
                    })
                }
            });

            $("#addBtn").click(function () {
                layer.open({
                    type: 2,
                    title: "添加",
                    shadeClose: true,
                    area: ["800px","600px"],
                    content: "{{route("crm.customer_field.create")}}",
                })
            })

        })
    </script>
@endsection
