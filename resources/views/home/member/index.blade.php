@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form" >
                <div class="layui-btn-group">

                        <button class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删 除</button>

                        <a class="layui-btn layui-btn-sm" href="{{ route('home.member.create') }}">添 加</a>

                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">

                        <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>

                        <a class="layui-btn layui-btn-sm" lay-event="role">角色</a>

                        <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">删除</a>

                </div>
            </script>
        </div>
    </div>
@endsection

@section('script')

        <script>
            layui.use(['layer', 'table', 'form'], function () {
                var $ = layui.jquery;
                var layer = layui.layer;
                var form = layui.form;
                var table = layui.table;
                //用户表格初始化
                var dataTable = table.render({
                    elem: '#dataTable'
                    , height: 500
                    , url: "{{ route('home.member.data') }}" //数据接口
                    , page: true //开启分页
                    , cols: [[ //表头
                        {checkbox: true,fixed: true}
                        ,{field: 'id', title: 'ID', sort: true,width:80}
                        ,{field: 'username', title: '帐号'}
                        ,{field: 'contact_name', title: '联系人'}
                        ,{field: 'contact_phone', title: '联系电话'}
                        ,{field: 'status_name', title: '状态', templet:function (d) {
                                if (d.status==1){
                                    return '<span class="layui-badge layui-bg-green">'+d.status_name+'</span>'
                                }else if (d.status==2){
                                    return '<span class="layui-badge layui-bg-cyan">'+d.status_name+'</span>'
                                }else {
                                    return ''
                                }
                            }}
                        ,{field: 'sip.username', title: '分机号',edit:true,templet:function (d) {
                                return d.sip.username;
                            }}
                        ,{field: 'created_at', title: '创建时间'}
                        ,{fixed: 'right', width: 200, align:'center', toolbar: '#options', title:'操作'}
                    ]]
                });

                //监听工具条
                table.on('tool(dataTable)', function (obj) { //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                    var data = obj.data //获得当前行数据
                        , layEvent = obj.event; //获得 lay-event 对应的值
                    if (layEvent === 'del') {
                        layer.confirm('确认删除吗？', function (index) {
                            layer.close(index)
                            var load = layer.load();
                            $.post("{{ route('home.member.destroy') }}", {
                                _method: 'delete',
                                ids: [data.id]
                            }, function (res) {
                                layer.close(load);
                                if (res.code == 0) {
                                    layer.msg(res.msg, {icon: 1}, function () {
                                        obj.del();
                                    })
                                } else {
                                    layer.msg(res.msg, {icon: 2})
                                }
                            });
                        });
                    } else if (layEvent === 'edit') {
                        location.href = '/home/member/' + data.id + '/edit';
                    } else if (layEvent === 'role') {
                        location.href = '/home/member/' + data.id + '/role';
                    }
                });

                //按钮批量删除
                $("#listDelete").click(function () {
                    var ids = [];
                    var hasCheck = table.checkStatus('dataTable');
                    var hasCheckData = hasCheck.data;
                    if (hasCheckData.length > 0) {
                        $.each(hasCheckData, function (index, element) {
                            ids.push(element.id)
                        })
                    }
                    if (ids.length > 0) {
                        layer.confirm('确认删除吗？', function (index) {
                            layer.close(index);
                            var load = layer.load();
                            $.post("{{ route('home.member.destroy') }}", {
                                _method: 'delete',
                                ids: ids
                            }, function (result) {
                                layer.close(load);
                                if (res.code == 0) {
                                    layer.msg(res.msg, {icon: 1}, function () {
                                        dataTable.reload({page: {curr: 1}});
                                    })
                                } else {
                                    layer.msg(res.msg, {icon: 2})
                                }
                            });
                        })
                    } else {
                        layer.msg('请选择删除项', {icon: 2});
                    }
                });

                //搜索
                form.on('submit(search)', function(data){
                    var parms = data.field;
                    dataTable.reload({
                        where:parms,
                        page:{curr:1}
                    });
                    return false;
                });

                //监听编辑
                table.on('edit(dataTable)', function(obj){ //注：edit是固定事件名，test是table原始容器的属性 lay-filter="对应的值"
                    console.log(obj.value); //得到修改后的值
                    console.log(obj.field); //当前编辑的字段名
                    console.log(obj.data); //所在行的所有相关数据
                    var load = layer.load();
                    var parm = {
                        "id" : obj.data.id,
                        "sip_id" : obj.value
                    };
                    $.post("{{route('home.member.assignSip')}}",parm,function (res) {
                        layer.close(load);
                        if (res.code == 0) {
                            layer.msg(res.msg, {icon: 1},function () {
                                dataTable.reload({
                                    page:{curr:1}
                                });
                            })
                        } else {
                            layer.msg(res.msg, {icon: 2})
                        }
                    })
                });

            })
        </script>

@endsection