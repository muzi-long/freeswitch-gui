@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form" action="{{route("crm.customer")}}">
                <div class="layui-btn-group">
                    @can('crm.waste.destroy')
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" data-url="{{route('crm.waste.destroy')}}" id="listDelete">删除</button>
                    @endcan
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
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('crm.waste.show')
                        <a class="layui-btn layui-btn-sm" lay-event="show">详情</a>
                    @endcan
                    @can('crm.waste.retrieve')
                        <a class="layui-btn layui-btn-sm" lay-event="retrieve">拾回</a>
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
            var laydate = layui.laydate;
            var upload = layui.upload;
            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 'full-200'
                ,url: "{{ route('crm.waste') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true}
                    ,{field: 'uuid', title: '客户编号'}
                    ,{field: 'name', title: '客户名称'}
                    ,{field: 'contact_name', title: '联系人'}
                    ,{field: 'node_name', title: '当前进度'}
                    ,{field: 'follow_time', title: '跟进时间'}
                    ,{field: 'follow_user_nickname', title: '跟进人'}
                    ,{field: 'remark', title: '跟进备注'}
                    ,{field: 'status_time', title: '剔除时间'}
                    ,{field: 'created_at', title: '录入时间'}
                    ,{fixed: 'right', width: 150, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if (layEvent === 'show'){
                    layer.open({
                        type: 2,
                        title: "详情",
                        shadeClose: true,
                        area: ["90%","90%"],
                        content: '/crm/waste/'+data.id+'/show',
                    })
                } else if (layEvent === 'retrieve'){
                    layer.confirm('确认拾回吗？', function(index){
                        layer.close(index);
                        var load = layer.load();
                        $.post("{{ route('crm.waste.retrieve') }}",{id:data.id},function (res) {
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

        })
    </script>
@endsection
