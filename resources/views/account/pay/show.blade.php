@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-row">
                <div class="layui-col-xs12">
                    <div class="layui-card">
                        <div class="layui-card-header"><b>订单信息</b></div>
                        <div class="layui-card-body">
                            <table class="layui-table" lay-skin="nob">
                                <tbody>
                                <tr>
                                    <td width="80" align="right">订单号：</td>
                                    <td>{{$order->num}}</td>
                                    <td width="80" align="right">总金额：</td>
                                    <td>{{$order->total_money}}</td>
                                    <td width="80" align="right">已付金额：</td>
                                    <td>{{$order->payed_money}}</td>
                                </tr>
                                <tr>
                                    <td width="80" align="right">前期款：</td>
                                    <td>{{$order->first_money}}</td>
                                    <td width="80" align="right">中期款：</td>
                                    <td>{{$order->mid_money}}</td>
                                    <td width="80" align="right">尾款：</td>
                                    <td>{{$order->last_money}}</td>
                                </tr>
                                <tr>
                                    <td width="80" align="right">客户名称：</td>
                                    <td>{{$order->name}}</td>
                                    <td width="80" align="right">联系人：</td>
                                    <td>{{$order->contact_name}}</td>
                                    <td width="80" align="right">联系电话：</td>
                                    <td>{{$order->contact_phone}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-row">
                <div class="layui-col-xs12">
                    <div class="layui-card">
                        <div class="layui-card-header"><b>付款申请记录</b></div>
                        <div class="layui-card-body">
                            <table id="dataTable" lay-filter="dataTable"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer','table','form','element','flow','laydate'],function () {
            var $ = layui.jquery;
            var form = layui.form;
            var flow = layui.flow;
            var laydate = layui.laydate;
            var table = layui.table;
            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 'full-200'
                ,url: "{{ route('account.pay.show',['order_id'=>$order->id]) }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true}
                    ,{field: 'money', title: '付款金额'}
                    ,{field: 'pay_type_name', title: '付款方式'}
                    ,{field: 'created_user_nickname', title: '申请人'}
                    ,{field: 'created_at', title: '申请时间',width:160}
                    ,{field: 'content', title: '申请备注'}
                    ,{field: 'status_name', title: '状态'}
                    ,{field: 'check_user_nickname', title: '审核人'}
                    ,{field: 'check_time', title: '审核时间',width:160}
                    ,{field: 'check_result', title:'审核结果'}
                ]]
            });
        });
    </script>
@endsection

