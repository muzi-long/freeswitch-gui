@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>分机统计</h2>
        </div>
        <div class="layui-card-body">
            <table class="layui-table">
                <colgroup>
                    <col align="center"><col>
                </colgroup>
                <thead>
                <tr>
                    <td rowspan="2"><b>员工</b></td>
                    <td rowspan="2"><b>分机</b></td>
                    <td align="center" colspan="3"><b>当日</b></td>
                    <td align="center" colspan="3"><b>本周</b></td>
                    <td align="center" colspan="3"><b>本月</b></td>
                </tr>
                <tr>
                    <td align="center">呼出</td>
                    <td align="center">接通</td>
                    <td align="center">接通率</td>
                    <td align="center">呼出</td>
                    <td align="center">接通</td>
                    <td align="center">接通率</td>
                    <td align="center">呼出</td>
                    <td align="center">接通</td>
                    <td align="center">接通率</td>
                </tr>
                </thead>
                <tbody>
                @forelse($sips as $sip)
                    <tr>
                        <td>{{$sip->merchant->contact_name}}</td>
                        <td>{{$sip->username}}</td>
                        <td align="center" style="color: red">{{$sip->todayCalls}}</td>
                        <td align="center" style="color: green">{{$sip->todaySuccessCalls}}</td>
                        <td align="center" style="color: #0000FF">{{$sip->todayRateCalls}}%</td>
                        <td align="center" style="color: red">{{$sip->weekCalls}}</td>
                        <td align="center" style="color: green">{{$sip->weekSuccessCalls}}</td>
                        <td align="center" style="color: #0000FF">{{$sip->weekRateCalls}}%</td>
                        <td align="center" style="color: red">{{$sip->monthCalls}}</td>
                        <td align="center" style="color: green">{{$sip->monthSuccessCalls}}</td>
                        <td align="center" style="color: #0000FF">{{$sip->monthRateCalls}}%</td>
                    </tr>
                @empty
                    <tr><td colspan="10" align="center">暂无数据</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer','table','form','laydate'],function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            var laydate = layui.laydate;

        })
    </script>
@endsection