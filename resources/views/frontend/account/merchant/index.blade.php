@extends('frontend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>商户资料</h2>
        </div>
        <div class="layui-card-body">
            <div class="layui-row">
                <div class="layui-col-xs5">
                    <div class="layui-card">
                        <div class="layui-card-header"><b>商户信息</b></div>
                        <div class="layui-card-body">
                            <table class="layui-table" lay-skin="nob">
                                <tbody>
                                <tr>
                                    <td width="80" align="right">公司名称：</td>
                                    <td>{{$model->company_name}}</td>
                                    <td width="80" align="right">到期时间：</td>
                                    <td>{{$model->expire_at}}</td>
                                </tr>
                                <tr>
                                    <td width="80" align="right">联系人：</td>
                                    <td>{{$model->contact_name}}</td>
                                    <td width="80" align="right">联系电话：</td>
                                    <td>{{$model->contact_phone}}</td>
                                </tr>
                                <tr>
                                    <td width="80" align="right">分机数量：</td>
                                    <td>{{$model->sip_num}}</td>
                                    <td width="80" align="right">网关数量：</td>
                                    <td>{{$model->gateway_num}}</td>
                                </tr>
                                <tr>
                                    <td width="80" align="right">坐席数量：</td>
                                    <td>{{$model->agent_num}}</td>
                                    <td width="80" align="right">队列数量：</td>
                                    <td>{{$model->queue_num}}</td>
                                </tr>
                                <tr>
                                    <td width="80" align="right">任务数量：</td>
                                    <td>{{$model->task_num}}</td>
                                    <td width="80" align="right">员工数量：</td>
                                    <td>{{$model->staff_num}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="layui-col-xs5 layui-col-lg-offset2">
                    <div class="layui-card">
                        <div class="layui-card-header"><b>登录帐号</b></div>
                        <div class="layui-card-body">
                            <table class="layui-table" lay-skin="nob">
                                <tbody>
                                <tr>
                                    <td width="80" align="right">姓名：</td>
                                    <td>{{$staff->nickname}}</td>
                                    <td width="80" align="right">帐号：</td>
                                    <td>{{$staff->username}}</td>
                                </tr>
                                <tr>
                                    <td width="80" align="right">登录时间：</td>
                                    <td>{{$staff->last_login_at}}</td>
                                    <td width="80" align="right">登录IP：</td>
                                    <td>{{$staff->last_login_ip}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

