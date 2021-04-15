@extends('frontend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">姓名</label>
                        <div class="layui-input-inline" style="width: 150px">
                            <input type="text" name="staff_nickname" placeholder="姓名" class="layui-input">
                        </div>

                        <label for="" class="layui-form-label">帐号</label>
                        <div class="layui-input-inline" style="width: 150px">
                            <input type="text" name="staff_username" placeholder="帐号" class="layui-input">
                        </div>

                        <label for="" class="layui-form-label">登录时间</label>
                        <div class="layui-input-inline" style="width: 150px">
                            <input type="text" name="time_start" id="created_at_start" placeholder="开始时间" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux">-</div>
                        <div class="layui-input-inline" style="width: 150px">
                            <input type="text" name="time_end" id="created_at_end" placeholder="结束时间" class="layui-input">
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
                ,url: "{{ route('frontend.system.staff.loginLog') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'department_name', title: '部门名称' }
                    ,{field: 'staff_nickname', title: '姓名'}
                    ,{field: 'staff_username', title: '帐号'}
                    ,{field: 'time', title: '登录时间'}
                    ,{field: 'ip', title: '登录ip'}
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
