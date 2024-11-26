@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form" action="{{route("crm.business")}}">
                <div class="layui-btn-group">
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
            @can('crm.business.to')
            <form class="layui-form" action="{{route("crm.business.to")}}">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">员工：</label>
                        <div class="layui-input-block" style="width: 275px">
                            @include('common.get_user')
                        </div>
                    </div>
                    <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="assignment_to" >分配</button>
                </div>
            </form>
            @endcan
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
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
                ,url: "{{ route('crm.business') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true}
                    ,{field: 'uuid', title: '客户编号'}
                    ,{field: 'name', title: '客户名称'}
                    ,{field: 'contact_name', title: '联系人'}
                    ,{field: 'contact_phone', title: '联系电话'}
                    ,{field: 'assignment_user_nickname', title: '分配人'}
                    ,{field: 'status_time', title: '分配时间'}
                    ,{field: 'created_at', title: '录入时间'}
                ]]
            });


            //分配
            form.on('submit(assignment_to)', function (data) {
                var ids = [];
                var hasCheck = table.checkStatus('dataTable');
                var hasCheckData = hasCheck.data;
                if (hasCheckData.length > 0) {
                    $.each(hasCheckData, function (index, element) {
                        ids.push(element.id)
                    })
                }
                if (ids.length === 0){
                    layer.msg('请选择分配项', {icon: 2});
                    return false
                }
                layer.confirm('确认分配吗？', function (index) {
                    layer.close(index);
                    let load = layer.load();
                    $.post(data.form.action, {ids:ids,user_id:data.field.user_id}, function (res) {
                        layer.close(load);
                        let code = res.code
                        layer.msg(res.msg, {time: 2000, icon: code == 0 ? 1 : 2}, function () {
                            if (code === 0) {
                                dataTable.reload()
                            }
                        });
                    });
                })
                return false;
            })

        })
    </script>
@endsection
