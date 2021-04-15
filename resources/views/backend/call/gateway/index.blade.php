@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form" action="{{route('backend.call.gateway.updateXml')}}">
                <div class="layui-btn-group">
                    @can('backend.call.gateway.destroy')
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删除</button>
                    @endcan
                    @can('backend.call.gateway.create')
                        <a class="layui-btn layui-btn-sm" href="{{ route('backend.call.gateway.create') }}">添加</a>
                    @endcan
                    @can('backend.call.gateway.updateXml')
                        <button class="layui-btn layui-btn-sm" type="button" lay-submit lay-filter="go" >更新配置</button>
                    @endcan
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">FS服务</label>
                    <div class="layui-input-inline">
                        <select name="fs_id" lay-verify="required">
                            <option value=""></option>
                            @foreach($fs as $d)
                                <option value="{{$d->id}}">{{$d->name}}({{$d->external_ip}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('backend.call.gateway.edit')
                        <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    @can('backend.call.gateway.destroy')
                        <a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del">删除</a>
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
                    , height: 500
                    , url: "{{ route('backend.call.gateway') }}" //数据接口
                    , page: true //开启分页
                    , cols: [[ //表头
                        {checkbox: true, fixed: true}
                        ,{field: 'id', title: 'ID', sort: true,width:80}
                        ,{field: 'name', title: '名称'}
                        ,{field: 'realm', title: '地址'}
                        ,{field: 'username', title: '帐号',templet:function(d){
                                return d.type==1?d.username:'';
                            }}
                        ,{field: 'password', title: '密码',templet:function(d){
                                return d.type==1?d.password:'';
                            }}
                        ,{field: 'prefix', title: '前缀'}
                        ,{field: 'outbound_caller_id', title: '出局号码'}
                        //,{field: 'status', title: '状态'}
                        ,{field: 'type', title: '对接方式',templet:function(d){
                                return d.type==1?'SIP':'IP';
                            }}
                        ,{field: 'status', title: '状态'}
                        ,{field: 'freeswitch_id', title: '服务器',templet:function(d){
                                return d.freeswitch.name;
                            }}
                        ,{field: 'merchant_id', title: '所属商户',templet:function(d){
                                return d.merchant.company_name;
                            }}
                        ,{field: 'created_at', title: '添加时间'}
                        , {fixed: 'right', width: 140, align: 'center', toolbar: '#options',title: '操作'}
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
                            $.post("{{ route('backend.call.gateway.destroy') }}", {
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
                        location.href = '/backend/call/gateway/' + data.id + '/edit';
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
                            $.post("{{ route('backend.call.gateway.destroy') }}", {
                                _method: 'delete',
                                ids: ids
                            }, function (res) {
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
                })
            })
        </script>

@endsection
