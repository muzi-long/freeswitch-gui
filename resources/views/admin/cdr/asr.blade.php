@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header">
            <span class="layui-bg-cyan">通话详单需要开启语音识别事件监听！！！</span>
        </div>
        <div class="layui-card-body">
            <table class="layui-table">
                <thead>
                <tr>
                    <th>UUID</th>
                    <th>内容</th>
                    <th>时间</th>
                </tr>
                </thead>
                <tbody>
                @forelse($record as $v)
                <tr>
                    <td>{{$v->uuid}}</td>
                    <td>{{$v->text}}</td>
                    <td>{{$v->created_at}}</td>
                </tr>
                @empty
                <tr><td colspan="3" align="center">暂无数据</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer','table','form','laydate'],function () {
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            var laydate = layui.laydate;

        })
    </script>
@endsection