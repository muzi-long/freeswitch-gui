@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div class="layui-btn-group">
                    @can('backend.crm.department.create')
                        <a class="layui-btn layui-btn-sm" href="{{ route('backend.crm.department.create') }}">添加</a>
                    @endcan
                        <button class="layui-btn layui-btn-sm" type="button" lay-submit lay-filter="search" >搜索</button>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">商户</label>
                        <div class="layui-input-inline">
                            <select name="merchant_id" >
                                <option value=""></option>
                                @foreach($merchants as $d)
                                    <option value="{{$d->id}}" >{{$d->company_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('backend.crm.department.edit')
                    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    @can('backend.crm.department.destroy')
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
                    url: "{{ route('backend.crm.department') }}",
                    where: where,
                    cols: [[ //表头
                        {field: 'id', title: 'ID', sort: true, width: 80}
                        , {field: 'name', title: '名称'}
                        , {field: 'merchant_id', title: '所属商户',templet: function (d) {
                                return d.merchant.company_name;
                            }}
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
                        $.post("{{ route('backend.crm.department.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                            if (result.code==0){
                                obj.del(); //删除对应行（tr）的DOM结构
                            }
                            layer.close(index);
                            var icon = result.code==0?1:2;
                            layer.msg(result.msg,{icon:icon})
                        });
                    });
                } else if(layEvent === 'edit'){
                    location.href = '/backend/crm/department/'+data.id+'/edit';
                }
            });

            //搜索
            form.on('submit(search)',function (data) {
                dataTable(data.field);
                return false;
            })

        })
    </script>
@endsection
