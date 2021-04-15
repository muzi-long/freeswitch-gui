@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div class="layui-btn-group">
                    @can('backend.crm.assignment.destroy')
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删除</button>
                    @endcan
                        <button class="layui-btn layui-btn-sm" type="button" lay-submit lay-filter="search" >搜索</button>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">商户</label>
                        <div class="layui-input-inline">
                            <select name="merchant_id" >
                                <option value=""></option>
                                @foreach($merchants as $d)
                                    <option value="{{$d->id}}" >{{$d->company_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">联系人：</label>
                        <div class="layui-input-block" style="width: 275px">
                            <input type="text" name="contact_name" placeholder="请输入姓名" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">电话：</label>
                        <div class="layui-input-block" style="width: 275px">
                            <input type="text" name="contact_phone" placeholder="请输入联系电话" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">公司名称：</label>
                        <div class="layui-input-block" style="width: 275px">
                            <input type="text" name="company_name" placeholder="请输入公司名称" class="layui-input" >
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
                ,height: 500
                ,url: "{{ route('backend.crm.assignment') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'merchant_id', title: '所属商户',templet: function (d) {
                            return d.merchant.company_name;
                        }}
                    ,{field: 'company_name', title: '公司名称'}
                    ,{field: 'contact_name', title: '姓名'}
                    ,{field: 'contact_phone', title: '联系电话'}
                    ,{field: 'created_at', title: '创建时间'}
                ]]
            });

            //搜索
            form.on('submit(search)',function(data) {
                dataTable.reload({
                    where: data.field,
                    page: {curr:1}
                });
                return false;
            });

            //批量删除
            $("#listDelete").click(function () {
                var ids = [];
                var hasCheck = table.checkStatus('dataTable');
                var hasCheckData = hasCheck.data;
                if (hasCheckData.length > 0) {
                    $.each(hasCheckData, function (index, element) {
                        ids.push(element.id)
                    })
                }
                if (ids.length > 0) {
                    layer.confirm('确认删除吗？', function (index) {
                        layer.close(index);
                        var load = layer.load();
                        $.post("{{ route('backend.crm.assignment.destroy') }}", {
                            _method: 'delete',
                            ids: ids
                        }, function (res) {
                            layer.close(load);
                            if (res.code == 0) {
                                layer.msg(res.msg, {icon: 1}, function () {
                                    dataTable.reload({page: {curr: 1}});
                                })
                            } else {
                                layer.msg(res.msg, {icon: 2})
                            }
                        });
                    })
                } else {
                    layer.msg('请选择删除项', {icon: 2});
                }
            })
        })
    </script>
@endsection
