@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                @can('call.sip.destroy')
                <button class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删除</button>
                @endcan
                @can('call.sip.create')
                <a class="layui-btn layui-btn-sm" id="addBtn" >添加</a>
                @endcan
                @can('call.sip.create_list')
                <a class="layui-btn layui-btn-sm" id="addListBtn">批量添加</a>
                @endcan
                @can('call.sip.updateXml')
                <button class="layui-btn layui-btn-sm" type="button" id="updateXml">更新配置</button>
                @endcan
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('call.sip.edit')
                    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    @can('call.sip.destroy')
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
                ,url: "{{ route('call.sip') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'username', title: '分机号'}
                    ,{field: 'password', title: '密码'}
                    ,{field: 'user', title: '绑定用户',templet:function (d) {
                            return d.user.nickname;
                        }}
                    ,{field: 'status_name', title: '注册状态'}
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
                        $.post("{{ route('call.sip.destroy') }}",{_method:'delete',ids:[data.id]},function (res) {
                            layer.close(index);
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
                        title: "添加",
                        shadeClose: true,
                        area: ["600px","400px"],
                        content: '/call/sip/'+data.id+'/edit',
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
                        $.post("{{ route('call.sip.destroy') }}",{_method:'delete',ids:ids},function (res) {
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
                layer.confirm('确认生成所有分机配置吗？', function (index) {
                    layer.close(index);
                    var load = layer.load();
                    $.post("{{ route('call.sip.updateXml') }}", {}, function (res) {
                        layer.close(load);
                        layer.msg(res.msg, {icon: res.code == 0 ? 1 : 2})
                    });
                })
            })

            $("#addBtn").click(function () {
                layer.open({
                    type: 2,
                    title: "添加",
                    shadeClose: true,
                    area: ["600px","400px"],
                    content: "{{route("call.sip.create")}}",
                })
            })
            $("#addListBtn").click(function () {
                layer.open({
                    type: 2,
                    title: "添加",
                    shadeClose: true,
                    area: ["600px","400px"],
                    content: "{{route("call.sip.create_list")}}",
                })
            })

        })
    </script>
@endsection
