@extends('base')

@section('content')
    <div class="layui-card">

        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                @can('system.user.destroy')
                    <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete" data-url="{{ route('system.user.destroy') }}">删除</button>
                @endcan
                @can('system.user.create')
                    <a class="layui-btn layui-btn-sm" id="addBtn">添加</a>
                @endcan
            </div>
        </div>

        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('system.user.edit')
                        <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    @can('system.user.role')
                        <a class="layui-btn layui-btn-sm" lay-event="role">角色</a>
                    @endcan
                    @can('system.user.destroy')
                        <a class="layui-btn layui-btn-danger layui-btn-sm " lay-event="del">删除</a>
                    @endcan
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
                    , height: 'full-200'
                    , url: "{{ route('system.user') }}"
                    , page: true //开启分页
                    , cols: [[ //表头
                        {checkbox: true, fixed: true}
                        , {field: 'id', title: 'ID', sort: true, width: 80}
                        , {field: 'name', title: '帐号'}
                        , {field: 'nickname', title: '昵称'}
                        , {field: 'phone', title: '手机号码'}
                        , {field: 'department_id', title: '部门',templet: function (res) {
                                return res.department.name;
                            }}
                        , {field: 'sip_id', title: '外呼号',templet: function (res) {
                                return res.sip.username;
                            }}
                        , {field: 'last_login_at', title: '最近登录时间'}
                        , {field: 'last_login_ip', title: '最近登录IP'}
                        , {field: 'status', title: '状态', templet: function (res) {
                                if (res.status == 1){
                                    return '<input type="checkbox" name="switch" lay-skin="switch" lay-text="启用|禁用" data-userid="'+res.id+'" lay-filter="status-switch" checked />';
                                }else {
                                    return '<input type="checkbox" name="switch" lay-skin="switch" lay-text="启用|禁用" data-userid="'+res.id+'" lay-filter="status-switch" />';
                                }
                            }}
                        , {field: 'created_at', title: '创建时间',width: 160}
                        , {fixed: 'right', align: 'center', toolbar: '#options'}
                    ]]
                });

                //监听工具条
                table.on('tool(dataTable)', function (obj) { //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                    var data = obj.data //获得当前行数据
                        , layEvent = obj.event; //获得 lay-event 对应的值
                    if (layEvent === 'del') {
                        deleteData(obj,"{{ route('system.user.destroy') }}");
                    } else if (layEvent === 'edit') {
                        layer.open({
                            type: 2,
                            title: "编辑",
                            shadeClose: true,
                            area: ["800px","600px"],
                            content: "/system/user/" + data.id + "/edit",
                        })
                    }
                });

                $("#addBtn").click(function () {
                    layer.open({
                        type: 2,
                        title: "添加",
                        shadeClose: true,
                        area: ["800px","600px"],
                        content: "{{route("system.user.create")}}",
                    })
                })

                form.on('switch(status-switch)', function(data){
                    var status = data.elem.checked ? 1 : 2;
                    var load = layer.load()
                    $.post("{{route("system.user.status")}}",{status:status,user_id:$(data.elem).data("userid")},function (res) {
                        layer.close(load);
                        layer.msg(res.msg, {icon: res.code == 0 ? 1 : 2})
                    })
                });

            })
        </script>

@endsection



