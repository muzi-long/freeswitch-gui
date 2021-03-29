@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form" action="{{route("order.order")}}">
                <div class="layui-btn-group">
                    @can('order.order.destroy')
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" data-url="{{route('crm.customer.destroy')}}" id="listDelete">删除</button>
                    @endcan
                        <button type="button" lay-submit lay-filter="search" class="layui-btn layui-btn-sm" >搜索</button>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">客户名称：</label>
                        <div class="layui-input-block" style="width: 275px">
                            <input type="text" name="name" placeholder="请输入名称" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">联系人：</label>
                        <div class="layui-input-block" style="width: 275px">
                            <input type="text" name="contact_name" placeholder="请输入联系人" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">联系电话：</label>
                        <div class="layui-input-block" style="width: 275px">
                            <input type="text" name="contact_phone" placeholder="请输入联系电话" class="layui-input" >
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('order.order.remark')
                        <a class="layui-btn layui-btn-sm" lay-event="remark">跟进</a>
                    @endcan
                    @can('order.order.pay')
                        <a class="layui-btn layui-btn-sm" lay-event="pay">付款</a>
                    @endcan
                </div>
            </script>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer','table','form','laydate','upload'],function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            var laydate = layui.laydate;
            var upload = layui.upload;
            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 'full-200'
                ,url: "{{ route('order.order') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true}
                    ,{field: 'num', title: '订单号'}
                    ,{field: 'total_money', title: '总金额'}
                    ,{field: 'payed_money', title: '已付金额'}
                    ,{field: 'name', title: '客户名称'}
                    ,{field: 'node_name', title: '当前进度'}
                    ,{field: 'follow_time', title: '跟进时间'}
                    ,{field: 'follow_user_nickname', title: '跟进人'}
                    ,{field: 'remark', title: '跟进备注'}
                    ,{field: 'status', title: '状态',templet:function (d) {
                            return d.status!=1?'<span class="layui-badge layui-bg-gray">生产中</span>':'<span class="layui-badge layui-bg-green">已完结</span>';
                        }}
                    ,{fixed: 'right', width: 250, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if (layEvent === 'remark'){
                    layer.open({
                        type: 2,
                        title: "备注",
                        shadeClose: true,
                        area: ["80%","80%"],
                        content: '/order/order/remark?id=' + data.id,
                    })
                } else if (layEvent === 'pay'){
                    layer.open({
                        type: 2,
                        title: "付款",
                        shadeClose: true,
                        area: ["600px","400px"],
                        content: '/order/order/payForm?id=' + data.id,
                    })
                }
            });

        })
    </script>
@endsection
