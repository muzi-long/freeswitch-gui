@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form" action="{{route("account.pay")}}">
                <div class="layui-btn-group">
                    <button type="button" lay-submit lay-filter="search" class="layui-btn layui-btn-sm" >搜索</button>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">订单号：</label>
                        <div class="layui-input-block" style="width: 275px">
                            <input type="text" name="num" placeholder="请输入名称" class="layui-input" >
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                    @{{# if(d.status==0){ }}
                    @can('account.pay.check')
                        <a class="layui-btn layui-btn-sm" lay-event="check">待审核</a>
                    @endcan
                    @{{# }else{ }}
                        <span class="layui-badge layui-bg-gray">@{{ d.status_name }}</span>
                    @{{# } }}
            </script>
            <script type="text/html" id="num">
                <a lay-event="show" style="cursor: pointer">@{{ d.order.num }}</a>
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
                ,url: "{{ route('account.pay') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true}
                    ,{field: 'num', title: '订单号',templet:function (d) {
                            return '<a lay-event="show" style="cursor: pointer">'+ d.order.num+ '</a>'
                        }}
                    ,{field: 'total_money', title: '总金额',templet:function (d) {
                            return d.order.total_money
                        }}
                    ,{field: 'payed_money', title: '已付金额',templet:function (d) {
                            return d.order.payed_money
                        }}
                    ,{field: 'money', title: '本次付款金额'}
                    ,{field: 'pay_type_name', title: '付款方式'}
                    ,{field: 'created_user_nickname', title: '申请人'}
                    ,{field: 'created_at', title: '申请时间'}
                    ,{field: 'content', title: '备注'}
                    ,{align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if (layEvent === 'check'){
                    layer.open({
                        type: 2,
                        title: "审核",
                        shadeClose: true,
                        area: ["600px","400px"],
                        content: '/account/pay/check?id='+data.id,
                    })
                } else if(layEvent == 'show'){
                    layer.open({
                        type: 2,
                        title: "付款申请记录",
                        shadeClose: true,
                        area: ["90%","90%"],
                        content: '/account/pay/show?order_id='+data.order_id,
                    })
                }
            });

        })
    </script>
@endsection
