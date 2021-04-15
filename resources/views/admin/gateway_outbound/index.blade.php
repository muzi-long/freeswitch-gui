@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form" >
                <div class="layui-btn-group">
                    <button type="button" class="layui-btn layui-btn-sm" id="create_btn">添 加</button>
                    <button class="layui-btn layui-btn-sm layui-btn-danger" type="button" id="listDelete">删 除</button>
                    <button class="layui-btn layui-btn-sm" type="button" id="importBtn" >导 入</button>
                    <button class="layui-btn layui-btn-sm" lay-submit lay-filter="search" >搜 索</button>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">网关</label>
                        <div class="layui-input-inline">
                            <select name="gateway_id" lay-search>
                                <option value="">请选择</option>
                                @foreach($gateways as $gateway)
                                    <option value="{{$gateway->id}}">{{$gateway->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">号码</label>
                        <div class="layui-input-inline">
                            <input type="text" name="number" placeholder="号码" class="layui-input">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                </div>
            </script>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer','table','form'],function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;

            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 500
                ,url: "{{ route('admin.gateway_outbound.data') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true}
                    ,{field: 'gateway_id', title: '网关', templet: function (d) {
                            return d.gateway.name;
                        }}
                    ,{field: 'number', title: '号码'}
                    ,{field: 'status', title: '状态', templet: function (d) {
                            return d.status==1?'启用':'禁用';
                        }}
                    ,{field: 'created_at', title: '添加时间'}
                    ,{align:'center', toolbar: '#options', title: '操作'}
                ]]
            });

            //添加
            $("#create_btn").click(function () {
                layer.open({
                    title:'添加',
                    type:2,
                    area:['460px','360px'],
                    shadeClose:true,
                    content:'{{route('admin.gateway_outbound.create')}}'
                })
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'edit'){
                    layer.open({
                        title:'编辑',
                        type:2,
                        area:['600px','480px'],
                        shadeClose:true,
                        content:'/admin/gateway_outbound/'+data.id+'/edit'
                    })
                }
            });

            //按钮批量删除
            $("#listDelete").click(function () {
                var ids = []
                var hasCheck = table.checkStatus('dataTable')
                var hasCheckData = hasCheck.data
                if (hasCheckData.length>0){
                    $.each(hasCheckData,function (index,element) {
                        ids.push(element.id)
                    })
                }
                if (ids.length>0){
                    layer.confirm('确认删除吗？', function(index){
                        layer.load();
                        $.post("{{ route('admin.gateway_outbound.destroy') }}",{_method:'delete',ids:ids},function (res) {
                            layer.closeAll();
                            layer.msg(res.msg,{time:2000},function () {
                                if (res.code==0){
                                    dataTable.reload({
                                        page:{curr:1}
                                    })
                                }
                            })
                        });
                    })
                }else {
                    layer.msg('请选择删除项')
                }
            })
            //搜索
            form.on('submit(search)', function(data){
                var parms = data.field;
                dataTable.reload({
                    where:parms,
                    page:{curr:1}
                });
                return false;
            });
            //导入
            $("#importBtn").click(function(){
                layer.open({
                    title:'导入，格式：name.csv',
                    type:2,
                    area:['600px','480px'],
                    shadeClose:true,
                    content:'/admin/gateway_outbound/importForm'
                })
            })
        })
    </script>
@endsection