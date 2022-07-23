@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-fluid" id="LAY-app-message-detail">
                <div class="layui-card layuiAdmin-msg-detail">
                    <div class="layui-card-header">
                        <h1>{{$model->title}}</h1>
                        <p>
                            {{ $model->created_at }}
                        </p>
                    </div>
                    <div class="layui-card-body layui-text">
                        <div class="layadmin-text">
                            {{ $model->content }}
                        </div>
                    </div>
                </div>
            </div>
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
            var dataTable = function () {
                treetable.render({
                    treeColIndex: 1,          // treetable新增参数
                    treeSpid: 0,             // treetable新增参数
                    treeIdName: 'id',       // treetable新增参数
                    treePidName: 'parent_id',     // treetable新增参数
                    treeDefaultClose: false,   // treetable新增参数
                    treeLinkage: false,        // treetable新增参数
                    elem: '#dataTable',
                    url: "{{ route('crm.department') }}",
                    cols: [[ //表头
                        {field: 'id', title: 'ID', sort: true, width: 80}
                        , {field: 'name', title: '名称'}
                        , {field: 'business_user_nickname', title: '部门经理'}
                        , {field: 'created_at', title: '创建时间'}
                        , {field: 'updated_at', title: '更新时间'}
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
                    deleteData(obj,"{{ route('crm.department.destroy') }}");
                } else if(layEvent === 'edit'){
                    layer.open({
                        type: 2,
                        title: "编辑",
                        shadeClose: true,
                        area: ["600px","400px"],
                        content: '/crm/department/'+data.id+'/edit',
                        end: function () {
                            dataTable();
                        }
                    })
                } else if(layEvent === 'create'){
                    layer.open({
                        type: 2,
                        title: "添加子部门",
                        shadeClose: true,
                        area: ["600px","400px"],
                        content: '/crm/department/create?parent_id=' + data.id,
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
                    area: ["600px","400px"],
                    content: "{{route("crm.department.create")}}",
                    end: function () {
                        dataTable();
                    }
                })
            })

        })
    </script>
@endsection
