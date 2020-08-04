@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div class="layui-btn-group">
                    @can('backend.crm.project.create')
                    <a class="layui-btn layui-btn-sm" href="{{ route('backend.crm.project.create') }}">添 加</a>
                    @endcan
                    <button lay-submit lay-filter="search" class="layui-btn layui-btn-sm" >搜索</button>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">商户：</label>
                        <div class="layui-input-inline" >
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
                        <div class="layui-input-inline" >
                            <input type="text" name="contact_name" placeholder="请输入姓名" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">联系电话：</label>
                        <div class="layui-input-inline" >
                            <input type="text" name="contact_phone" placeholder="请输入联系电话" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">公司名称：</label>
                        <div class="layui-input-inline" >
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
                    @can('backend.crm.project.destroy')
                        <a class="layui-btn layui-btn-danger layui-btn-sm " lay-event="del">删除</a>
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
                ,height: 500
                ,url: "{{ route('backend.crm.project') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'merchant_id', title: '所属商户',templet: function (d) {
                            return d.merchant.company_name;
                        }}
                    ,{field: 'company_name', title: '公司名称'}
                    ,{field: 'contact_name', title: '联系人'}
                    ,{field: 'contact_phone', title: '联系电话'}
                    ,{field: 'node_id', title: '当前节点',templet:function (d) {
                            return d.node.name;
                        }}
                    ,{field: 'follow_user_id', title: '跟进人',templet:function (d) {
                            return d.follow_user.nickname;
                        }}
                    ,{field: 'follow_at', title: '跟进时间'}
                    ,{field: 'remark', title: '跟进备注',width:140}
                    ,{field: 'next_follow_at', title: '下次跟进时间'}
                    ,{field: 'created_at', title: '创建时间'}
                    ,{fixed: 'right', width: 250, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'del'){
                    layer.confirm('删除后客户将进入公海库，所有人可拾回。确认删除吗？', function(index){
                        layer.close(index);
                        var load = layer.load();
                        $.post("{{ route('backend.crm.project.destroy') }}",{_method:'delete',ids:[data.id]},function (res) {
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
