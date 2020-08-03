@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div class="layui-btn-group">
                    @can('backend.crm.waste.destroy')
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
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('backend.crm.waste.show')
                        <a class="layui-btn layui-btn-sm" lay-event="show">跟进记录</a>
                    @endcan
                </div>
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
            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 500
                ,url: "{{ route('backend.crm.waste') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'merchant_id', title: '所属商户',templet: function (d) {
                            return d.merchant.company_name;
                        }}
                    ,{field: 'company_name', title: '公司名称'}
                    ,{field: 'contact_name', title: '联系人'}
                    ,{field: 'contact_phone', title: '联系电话'}
                    ,{fixed: 'right', width: 150, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'show'){
                    location.href = '/backend/crm/waste/'+data.id+'/show';
                }
            });

            //搜索
            form.on('submit(search)',function(data) {
                dataTable.reload({
                    where: data.field,
                    page: {curr:1}
                });
                return false;
            });

        })
    </script>
@endsection
