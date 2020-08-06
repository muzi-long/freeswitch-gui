@extends('frontend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">分机号</label>
                        <div class="layui-input-inline">
                            <input type="text" name="username" class="layui-input" placeholder="输入分机号" maxlength="4">
                        </div>
                    </div>
                    <button class="layui-btn layui-btn-sm" type="button" lay-submit lay-filter="search" >搜索</button>
                </div>

            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @{{# if(d.staff_id) { }}
                        <a class="layui-btn layui-btn-sm layui-btn-danger" lay-event="unbind">解绑</a>
                    @{{# }else{ }}
                        <a class="layui-btn layui-btn-sm" lay-event="bind">绑定</a>
                    @{{# } }}
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
                    , url: "{{ route('frontend.call.sip') }}" //数据接口
                    , page: true //开启分页
                    , cols: [[ //表头
                        {field: 'username', title: '分机号'}
                        ,{field: 'password', title: '密码'}
                        ,{field: 'freeswitch_id', title: '地址',templet:function(d){
                                return d.freeswitch.external_ip+":"+d.freeswitch.internal_sip_port
                            }}
                        ,{field: 'status_name', title: '注册状态'}
                        ,{field: 'staff_id', title: '绑定员工',templet:function(d){
                                return d.staff.nickname;
                            }}
                        ,{field: 'bind_time', title: '绑定时间时间'}
                        , {fixed: 'right', width: 140, align: 'center', toolbar: '#options',title: '操作'}
                    ]]
                });

                //监听工具条
                table.on('tool(dataTable)', function (obj) { //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                    var data = obj.data //获得当前行数据
                        , layEvent = obj.event; //获得 lay-event 对应的值
                    if (layEvent === 'unbind') {
                        layer.confirm('确认解除绑定吗？', function (index) {
                            layer.close(index)
                            var load = layer.load();
                            $.post("{{ route('frontend.call.sip.unbind') }}", {
                                sip_id: data.id,
                                staff_id: data.staff_id,
                            }, function (res) {
                                layer.close(load);
                                layer.msg(res.msg,{icon:res.code == 0?1:2},function () {
                                    if (res.code==0){
                                        dataTable.reload();
                                    }
                                })
                            });
                        });
                    } else if (layEvent === 'bind') {
                        layer.open({
                            title:'绑定员工',
                            type:2,
                            area:['460px','400px'],
                            shadeClose:true,
                            content:'/frontend/call/sip/'+data.id+'/bindForm'
                        })
                    }
                });

                //搜索
                form.on('submit(search)',function (data) {
                    dataTable.reload({
                        where:data.field,
                        page:{curr: 1}
                    })
                    return false;
                })
            })
        </script>

@endsection
