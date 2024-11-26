@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form" action="{{route('chat.message.read')}}">
                <div class="layui-btn-group">
                    @can('chat.message.destroy')
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete" data-url="{{ route('chat.message.destroy') }}">删除</button>
                    @endcan
                    @can('chat.message.create')
                        <a class="layui-btn layui-btn-sm" id="addBtn" >发送消息</a>
                    @endcan
                    @can('chat.message.read')
                        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="read" data-type="check" >标记已读</button>
                    @endcan
                    @can('chat.message.read')
                        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="read" data-type="all" >全部已读</button>
                    @endcan
                </div>
            </form>
        </div>
        <div class="layui-card">
            <div class="layui-card-body">
                <table id="dataTable" lay-filter="dataTable"></table>
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
            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 'full-200'
                ,url: "{{ route('chat.message') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true}
                    ,{field: 'read', title: '状态',width:80,templet:function (d) {
                            return d.read==0?'<span class="layui-badge layui-bg-black">未读</span>':'<span class="layui-badge layui-bg-gray">已读</span>'
                        }}
                    ,{field: 'title', title: '标题',width:200,templet:function (d) {
                            return '<a lay-event="show" title="点击查看详情">'+d.title+'</a>'
                        }}
                    ,{field: 'content', title: '内容',width:700}
                    ,{field: 'send_user_nickname', title: '发送人',templet:function (d) {
                            return d.send_user_id == 0 ? '系统' : d.send_user_nickname;
                        }}
                    ,{field: 'accept_user_nickname', title: '接收人'}
                    ,{field: 'created_at', title: '发送时间'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'show'){
                    layer.open({
                        type: 2,
                        title: "消息详情",
                        shadeClose: true,
                        area: ["600px","400px"],
                        content: "/chat/message/"+data.id+"/show",
                        end:function () {
                            dataTable.reload()
                        }
                    })
                }
            });

            $("#addBtn").click(function () {
                layer.open({
                    type: 2,
                    title: "发送消息",
                    shadeClose: true,
                    area: ["600px","400px"],
                    content: "{{route("chat.message.create")}}",
                })
            })

            form.on('submit(read)',function (data) {
                var ids = [];
                var type = $(data.elem).data('type')
                if(type !== 'all'){
                    var hasCheck = table.checkStatus('dataTable');
                    var hasCheckData = hasCheck.data;
                    var url = $(this).data('url');
                    if (hasCheckData.length > 0) {
                        $.each(hasCheckData, function (index, element) {
                            ids.push(element.id)
                        })
                    }
                    if (ids.length === 0){
                        layer.msg('请选择操作项',{time:1500,icon:2})
                        return false
                    }
                }

                layer.confirm('确认操作吗？',function (index) {
                    layer.close(index)
                    var load = layer.load();
                    $.post(data.form.action, {ids:ids,type:type}, function (res) {
                        layer.close(load);
                        var code = res.code
                        layer.msg(res.msg, {time: 2000, icon: code === 0 ? 1 : 2}, function () {
                            if (code === 0) {
                                dataTable.reload()
                            }
                        });
                    });
                })

                return false;
            })

        })
    </script>
@endsection
