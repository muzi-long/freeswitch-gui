@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form  class="layui-form">
                <button class="layui-btn layui-btn-sm" lay-submit lay-filter="search" >搜索</button>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">用户</label>
                        <div class="layui-input-block">
                            <select name="user_id">
                                <option value=""></option>
                                @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->nickname}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">呼叫时间</label>
                        <div class="layui-input-inline" style="width: 160px">
                            <input type="text" name="creatad_at_start" id="creatad_at_start" class="layui-input" placeholder="开始时间">
                        </div>
                        <div class="layui-form-mid layui-word-aux">-</div>
                        <div class="layui-input-inline" style="width: 160px">
                            <input type="text" name="creatad_at_end" id="creatad_at_end" class="layui-input" placeholder="结束时间">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
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
            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 500
                ,toolbar: true
                ,url: "{{ route('data_view.cdr') }}" //数据接口
                ,page: false //开启分页
                ,cols: [[
                    {align: 'center', title: '用户', rowspan: 2,field:'nickname'},
                    {align: 'center', title: '当日', colspan: 5},
                    {align: 'center', title: '本周', colspan: 5},
                    {align: 'center', title: '本月', colspan: 5},
                ],[ //表头

                    {field: 'todayCalls', title: '呼出', sort: true,align: 'center',style:'color:red'}
                    ,{field: 'todaySuccessCalls', title: '接通', sort: true,align: 'center',style:'color:green'}
                    ,{field: 'todayRateCalls', title: '接通率', sort: true,align: 'center',style:'color:#0000FF'}
                    ,{field: 'todayThirtyCalls', title: '30秒以上', sort: true,align: 'center',}
                    ,{field: 'todaySixtyCalls', title: '60秒以上', sort: true,align: 'center',}

                    ,{field: 'weekCalls', title: '呼出', sort: true,align: 'center',style:'color:red'}
                    ,{field: 'weekSuccessCalls', title: '接通', sort: true,align: 'center',style:'color:green'}
                    ,{field: 'weekRateCalls', title: '接通率', sort: true,align: 'center',style:'color:#0000FF'}
                    ,{field: 'weekThirtyCalls', title: '30秒以上', sort: true,align: 'center',}
                    ,{field: 'weekSixtyCalls', title: '60秒以上', sort: true,align: 'center',}

                    ,{field: 'monthCalls', title: '呼出', sort: true,align: 'center',style:'color:red'}
                    ,{field: 'monthSuccessCalls', title: '接通', sort: true,align: 'center',style:'color:green'}
                    ,{field: 'monthRateCalls', title: '接通率', sort: true,align: 'center',style:'color:#0000FF'}
                    ,{field: 'monthThirtyCalls', title: '30秒以上', sort: true,align: 'center',}
                    ,{field: 'monthSixtyCalls', title: '60秒以上', sort: true,align: 'center',}

                ]]
            });

            //时间选择
            laydate.render({type: 'datetime', elem: '#created_at_start'});
            laydate.render({type: 'datetime', elem: '#created_at_end'});

        })
    </script>
@endsection
