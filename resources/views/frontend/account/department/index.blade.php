@extends('frontend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div class="layui-btn-group">
                    @can('frontend.account.department.create')
                        <a class="layui-btn layui-btn-sm" href="{{ route('frontend.account.department.create') }}">添加</a>
                    @endcan
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('frontend.account.department.edit')
                    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    @can('frontend.account.department.destroy')
                    <a class="layui-btn layui-btn-danger layui-btn-sm " lay-event="del">删除</a>
                    @endcan
                </div>
            </script>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.config({
            base: '/layuiadmin/modules/'
        }).extend({
            treetable: 'treetable-lay/treetable'
        }).use(['layer', 'table', 'form', 'treetable'], function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            var treetable = layui.treetable;
            // 渲染表格
            var dataTable = function (where) {
                treetable.render({
                    treeColIndex: 1,          // treetable新增参数
                    treeSpid: 0,             // treetable新增参数
                    treeIdName: 'id',       // treetable新增参数
                    treePidName: 'parent_id',     // treetable新增参数
                    treeDefaultClose: false,   // treetable新增参数
                    treeLinkage: false,        // treetable新增参数
                    elem: '#dataTable',
                    url: "{{ route('frontend.account.department') }}",
                    where: where,
                    cols: [[ //表头
                        {field: 'id', title: 'ID', sort: true, width: 80}
                        , {field: 'name', title: '名称'}
                        , {field: 'sort', title: '序号'}
                        , {field: 'created_at', title: '创建时间'}
                        , {fixed: 'right',title:'操作', width: 260, align: 'center', toolbar: '#options'}
                    ]]
                });
            }
            dataTable(); //调用此函数可重新渲染表格

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        $.post("{{ route('frontend.account.department.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                            if (result.code==0){
                                obj.del(); //删除对应行（tr）的DOM结构
                            }
                            layer.close(index);
                            var icon = result.code==0?1:2;
                            layer.msg(result.msg,{icon:icon})
                        });
                    });
                } else if(layEvent === 'edit'){
                    location.href = '/frontend/account/department/'+data.id+'/edit';
                }
            });
        })
    </script>
@endsection
