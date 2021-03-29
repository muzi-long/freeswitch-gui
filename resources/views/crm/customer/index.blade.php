@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form" action="{{route("crm.customer")}}">
                <div class="layui-btn-group">
                    @can('crm.customer.destroy')
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" data-url="{{route('crm.customer.destroy')}}" id="listDelete">删除</button>
                    @endcan
                    @can('crm.customer.create')
                        <a class="layui-btn layui-btn-sm" id="addBtn">添加</a>
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
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">联系电话：</label>
                        <div class="layui-input-block" style="width: 275px">
                            <input type="text" name="contact_phone" placeholder="请输入联系电话" class="layui-input" >
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">节点进度：</label>
                        <div class="layui-input-block" style="width: 275px">
                            @include('common.get_node',['type'=>2,'node_id'=>0])
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">跟进时间：</label>
                        <div class="layui-input-inline" style="width: 140px">
                            <input type="text" id="follow_time_start" name="follow_time_start" placeholder="请选择开始时间" class="layui-input" readonly >
                        </div>
                        <div class="layui-form-mid layui-word-aux">-</div>
                        <div class="layui-input-inline" style="width: 140px">
                            <input type="text" id="follow_time_end" name="follow_time_end" placeholder="请选择结束时间" class="layui-input" readonly >
                        </div>
                    </div>
                </div>
            </form>
            @can('crm.customer.transfer')
            <form class="layui-form" action="{{route("crm.customer.transfer")}}">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">员工：</label>
                        <div class="layui-input-block" style="width: 275px">
                            @include('common.get_user')
                        </div>
                    </div>
                    <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="assignment_to" >移交</button>
                </div>
            </form>
            @endcan
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('crm.customer.show')
                        <a class="layui-btn layui-btn-sm" lay-event="show">详情</a>
                    @endcan
                    @can('crm.customer.edit')
                        <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    @can('crm.customer.remark')
                        <a class="layui-btn layui-btn-sm" lay-event="remark">跟进</a>
                    @endcan
                    @can('crm.customer.remove')
                        <a class="layui-btn layui-btn-sm" lay-event="remove">剔除</a>
                    @endcan
                    @can('order.order.create')
                        <a class="layui-btn layui-btn-sm" lay-event="order">下单</a>
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
                ,url: "{{ route('crm.customer') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true}
                    ,{field: 'uuid', title: '客户编号'}
                    ,{field: 'name', title: '客户名称'}
                    ,{field: 'contact_name', title: '联系人'}
                    ,{field: 'contact_phone', title: '联系电话'}
                    ,{field: 'node_name', title: '当前进度'}
                    ,{field: 'follow_time', title: '跟进时间'}
                    ,{field: 'follow_user_nickname', title: '跟进人'}
                    ,{field: 'remark', title: '跟进备注'}
                    ,{field: 'is_end', title: '是否成单',templet:function (d) {
                            return d.is_end!=1?'<span class="layui-badge layui-bg-gray">跟进中</span>':'<span class="layui-badge layui-bg-green">已成单</span>';
                        }}
                    ,{fixed: 'right', width: 250, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'edit'){
                    layer.open({
                        type: 2,
                        title: "编辑",
                        shadeClose: true,
                        area: ["90%","90%"],
                        content: '/crm/customer/'+data.id+'/edit',
                    })
                } else if (layEvent === 'show'){
                    layer.open({
                        type: 2,
                        title: "详情",
                        shadeClose: true,
                        area: ["90%","90%"],
                        content: '/crm/customer/'+data.id+'/show',
                    })
                } else if (layEvent === 'remark'){
                    layer.open({
                        type: 2,
                        title: "备注",
                        shadeClose: true,
                        area: ["600px","600px"],
                        content: '/crm/customer/'+data.id+'/remark',
                    })
                } else if (layEvent === 'remove'){
                    layer.confirm('剔除后客户将进入公海库，所有人可拾回。确认剔除吗？', function(index){
                        layer.close(index);
                        var load = layer.load();
                        $.post("{{ route('crm.customer.remove') }}",{customer_ids:[data.id]},function (res) {
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
                } else if (layEvent === 'order'){
                    layer.open({
                        type: 2,
                        title: "下单",
                        shadeClose: true,
                        area: ["800px","600px"],
                        content: '/order/order/create?customer_id='+data.id,
                    })
                }
            });
            $("#addBtn").click(function () {
                layer.open({
                    type: 2,
                    title: "添加",
                    shadeClose: true,
                    area: ["90%","90%"],
                    content: "{{route("crm.customer.create")}}",
                })
            })


            //移交
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
                    layer.msg('请选择移交项', {icon: 2});
                    return false
                }
                layer.confirm('确认移交吗？', function (index) {
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


            laydate.render({elem: '#follow_time_start', type: 'datetime'})
            laydate.render({elem: '#follow_time_end', type: 'datetime'})
        })
    </script>
@endsection
