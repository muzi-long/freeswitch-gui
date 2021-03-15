@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                @can('call.gateway.destroy')
                <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删除</button>
                @endcan
                @can('call.gateway.create')
                <a class="layui-btn layui-btn-sm" id="addBtn">添加</a>
                @endcan
                @can('call.gateway.updateXml')
                <button type="button" class="layui-btn layui-btn-sm" id="updateXml">更新配置</button>
                @endcan
            </div>

        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('call.gateway.edit')
                    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    @can('call.gateway.destroy')
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
                ,url: "{{ route('call.gateway') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
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
                    //,{field: 'status', title: '状态'}
                    ,{field: 'created_at', title: '添加时间'}
                    ,{fixed: 'right', width: 220, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        layer.close(index);
                        var load = layer.load()
                        $.post("{{ route('call.gateway.destroy') }}",{_method:'delete',ids:[data.id]},function (res) {
                            layer.close(load);
                            layer.msg(res.msg,{time:2000,icon:res.code==0?1:2},function () {
                                if (res.code==0){
                                    obj.del(); //删除对应行（tr）的DOM结构
                                }
                            })
                        });
                    });
                } else if(layEvent === 'edit'){
                    layer.open({
                        type: 2,
                        title: "编辑",
                        shadeClose: true,
                        area: ["600px","400px"],
                        content: '/call/gateway/'+data.id+'/edit',
                    })
                }
            });

            //按钮批量删除
            $("#listDelete").click(function () {
                var ids = []
                var hasCheck = table.checkStatus('dataTable')
                var hasCheckData = hasCheck.data
                if (hasCheckData.length>0){
                    $.each(hasCheckData,function (index,element) {
                        ids.push(element.id)
                    })
                }
                if (ids.length>0){
                    layer.confirm('确认删除吗？', function(index){
                        layer.close(index);
                        var load = layer.load()
                        $.post("{{ route('call.gateway.destroy') }}",{_method:'delete',ids:ids},function (res) {
                            layer.close(load);
                            layer.msg(res.msg,{time:2000,icon:res.code==0?1:2},function () {
                                if (res.code==0){
                                    dataTable.reload()
                                }
                            })
                        });
                    })
                }else {
                    layer.msg('请选择删除项',{icon:2})
                }
            })

            //更新配置
            $("#updateXml").click(function () {
                layer.confirm('该操作将重新注册所有网关，确认操作吗？', function(index){
                    layer.close(index);
                    var load = layer.load()
                    $.post("{{ route('call.gateway.updateXml') }}",{},function (res) {
                        layer.close(load);
                        var icon = res.code==0?1:2;
                        layer.msg(res.msg,{icon:icon})
                    });
                })
            })
            $("#addBtn").click(function () {
                layer.open({
                    type: 2,
                    title: "添加",
                    shadeClose: true,
                    area: ["600px","400px"],
                    content: "{{route("call.gateway.create")}}",
                })
            })
        })
    </script>
@endsection
