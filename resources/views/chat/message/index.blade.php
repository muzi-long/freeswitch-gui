@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div class="layui-btn-group">
                    @can('chat.message.destroy')
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete" data-url="{{ route('chat.message.destroy') }}">删除</button>
                    @endcan
                    @can('chat.message.create')
                        <a class="layui-btn layui-btn-sm" id="addBtn" >发送消息</a>
                    @endcan
                    @can('chat.message.read')
                        <a class="layui-btn layui-btn-sm" id="addBtn" >标记已读</a>
                    @endcan
                    @can('chat.message.read')
                        <a class="layui-btn layui-btn-sm" id="addBtn" >全部已读</a>
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
                    ,{field: 'title', title: '标题',width:200}
                    ,{field: 'content', title: '内容',width:700}
                    ,{field: 'send_user_nickname', title: '发送人',templet:function (d) {
                            return d.send_user_id == 0 ? '系统' : d.send_user_nickname;
                        }}
                    ,{field: 'accept_user_nickname', title: '接收人'}
                    ,{field: 'created_at', title: '发送时间'}
                ]]
                ,done: function (res, curr, count) {
                    trNum = count;
                    for(var i = 0;i<res.data.length;i++){
                        var state = res.data[i].checkStatus;
                        if(res.data[i].read == 1){
                            var index = res.data[i]['LAY_TABLE_INDEX'];
                            $(".layui-table tr[data-index="+index+"] input[type='checkbox']").prop('disabled',true);
                            $(".layui-table tr[data-index="+index+"] input[type='checkbox']").next().addClass('layui-btn-disabled');


                            //$(".layui-table tr[data-index="+index+"] td:first-child").html('');
                            //$(".layui-table tr[data-index="+index+"] input[type='checkbox']").remove();
                        }
                    }
                }
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'show'){

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

        })
    </script>
@endsection
