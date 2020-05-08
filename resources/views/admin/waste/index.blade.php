@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div class="layui-form-item">

                    <div class="layui-inline">
                        <label for="" class="layui-form-label">公司名称：</label>
                        <div class="layui-input-block" style="width: 275px">
                            <input type="text" name="company_name" placeholder="请输入公司名称" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">姓名：</label>
                        <div class="layui-input-block" style="width: 275px">
                            <input type="text" name="name" placeholder="请输入姓名" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">电话：</label>
                        <div class="layui-input-block" style="width: 275px">
                            <input type="text" name="phone" placeholder="请输入联系电话" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-inline">
                        <button lay-submit lay-filter="search" class="layui-btn layui-btn-sm" >搜索</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    <a class="layui-btn layui-btn-sm layui-btn-warm" lay-event="retrieve">拾回</a>
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
                ,url: "{{ route('admin.waste.data') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'company_name', title: '公司名称'}
                    ,{field: 'name', title: '姓名'}
                    ,{field: 'phone', title: '联系电话'}
                    ,{field: 'deleted_at', title: '删除时间'}
                    ,{fixed: 'right', width: 150, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'retrieve'){
                    layer.confirm('确认拾回吗？', function(index){
                        layer.close(index);
                        var load = layer.load();
                        $.post("{{ route('admin.waste.retrieve') }}",{id:data.id},function (res) {
                            layer.close(load);
                            if (res.code == 0) {
                                layer.msg(res.msg, {icon: 1}, function () {
                                    obj.del();
                                })
                            } else {
                                layer.msg(res.msg, {icon: 2})
                            }
                        });
                    });
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