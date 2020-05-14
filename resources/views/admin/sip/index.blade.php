@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                <button class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删 除</button>
                <a class="layui-btn layui-btn-sm" href="{{ route('admin.sip.create') }}">添 加</a>
                <a class="layui-btn layui-btn-sm" href="{{ route('admin.sip.create_list') }}">批量添加</a>
                <button class="layui-btn layui-btn-sm" type="button" id="updateXml">更新配置</button>
                <button class="layui-btn layui-btn-sm" type="button" id="updateGateway">切换网关</button>
            </div>

        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    <a class="layui-btn layui-btn-danger layui-btn-sm " lay-event="del">删除</a>
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
            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 500
                ,url: "{{ route('admin.sip') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'username', title: '分机号'}
                    ,{field: 'password', title: '密码'}
                    ,{field: 'effective_caller_id_name', title: '外显名称'}
                    ,{field: 'effective_caller_id_number', title: '外显号码'}
                    ,{field: 'outbound_caller_id_name', title: '出局名称'}
                    ,{field: 'outbound_caller_id_number', title: '出局号码'}
                    ,{field: 'status', title: '状态'}
                    ,{field: 'created_at', title: '添加时间'}
                    ,{fixed: 'right', width: 220, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        $.post("{{ route('admin.sip.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                            if (result.code==0){
                                obj.del(); //删除对应行（tr）的DOM结构
                            }
                            layer.close(index);
                            var icon = result.code==0?6:5;
                            layer.msg(result.msg,{icon:icon})
                        });
                    });
                } else if(layEvent === 'edit'){
                    location.href = '/admin/sip/'+data.id+'/edit';
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
                        $.post("{{ route('admin.sip.destroy') }}",{_method:'delete',ids:ids},function (result) {
                            if (result.code==0){
                                dataTable.reload()
                            }
                            layer.close(index);
                            var icon = result.code==0?1:2;
                            layer.msg(result.msg,{icon:icon})
                        });
                    })
                }else {
                    layer.msg('请选择删除项',{icon:5})
                }
            })
            //更新配置
            $("#updateXml").click(function () {
                    layer.confirm('确认生成所有分机配置吗？', function (index) {
                        layer.close(index);
                        layer.load();
                        $.post("{{ route('admin.sip.updateXml') }}", {}, function (result) {
                            layer.closeAll();
                            var icon = result.code == 0 ? 1 : 2;
                            layer.msg(result.msg, {icon: icon})
                        });
                    })
            })

            //切换网关
            $("#updateGateway").click(function(){
                layer.open({
                    title:'批量更新网关',
                    type:2,
                    area:['600px','400px'],
                    shadeClose:true,
                    content:'{{route('admin.sip.updateGatewayForm')}}'
                })
            })

        })
    </script>
@endsection