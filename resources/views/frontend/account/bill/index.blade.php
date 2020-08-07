@extends('frontend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">类型</label>
                        <div class="layui-input-inline" style="width: 100px;">
                            <select name="type">
                                <option value="">请选择</option>
                                <option value="1">增加</option>
                                <option value="2">减少</option>
                            </select>
                        </div>

                        <label for="" class="layui-form-label">时间</label>
                        <div class="layui-input-inline" style="width: 150px">
                            <input type="text" name="created_at_start" id="created_at_start" placeholder="开始时间" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux">-</div>
                        <div class="layui-input-inline" style="width: 150px">
                            <input type="text" name="created_at_end" id="created_at_end" placeholder="结束时间" class="layui-input">
                        </div>
                        <div class="layui-input-inline" style="width: 100px">
                            <button type="button" class="layui-btn" lay-submit lay-filter="search" >搜 索</button>
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
        layui.use(['layer','table','form','element','laydate'],function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            var laydate = layui.laydate;

            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 500
                ,url: "{{ route('frontend.account.bill') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'type', title: '类型', width: 80, templet:function (d) {
                            return d.type==1?'<span class="layui-badge layui-bg-green">增加</span>':'<span class="layui-badge layui-bg-cyan">减少</span>';
                        }}
                    ,{field: 'money_format', title: '变动金额（元）', width: 120}
                    ,{field: 'total_format', title: '余额（元）', width: 120}
                    ,{field: 'remark', title: '备注'}
                    ,{field: 'admin_name', title: '操作人'}
                    ,{field: 'created_at', title: '时间'}
                ]]
            });
            //时间选择
            laydate.render({elem:'#created_at_start',type:'datetime'})
            laydate.render({elem:'#created_at_end',type:'datetime'})
            //搜索
            form.on('submit(search)', function (data) {
                parms = data.field;
                dataTable.reload({
                    where:parms,
                    page:{curr:1}
                });
                return false;
            });
        })
    </script>
@endsection
