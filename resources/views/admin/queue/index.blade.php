@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div class="layui-btn-group">
                    <button class="layui-btn layui-btn-sm layui-btn-danger" type="button" id="listDelete">删 除</button>
                    <a class="layui-btn layui-btn-sm" href="{{ route('admin.queue.create') }}">添 加</a>
                    <button class="layui-btn layui-btn-sm" type="button" id="updateXml">更新配置</button>
                    <button class="layui-btn layui-btn-sm" lay-submit lay-filter="search" >搜 索</button>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">队列名称</label>
                        <div class="layui-input-inline">
                            <input type="text" name="display_name" placeholder="" class="layui-input">
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
                    <a class="layui-btn layui-btn-sm" lay-event="agent">分配坐席</a>
                    <a class="layui-btn layui-btn-danger layui-btn-sm " lay-event="del">删除</a>
                </div>
            </script>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer','table','form','jquery'],function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 500
                ,url: "{{ route('admin.queue.data') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true}
                    ,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'display_name', title: '队列名称'}
                    ,{field: 'strategy_name', title: '振铃策略'}
                    ,{field: 'agents_count', title: '坐席总数'}
                    ,{field: 'max_wait_time', title: '超时时间'}
                    ,{field: 'created_at', title: '添加时间'}
                    ,{ width: 220, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        $.post("{{ route('admin.queue.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                            if (result.code==0){
                                obj.del(); //删除对应行（tr）的DOM结构
                            }
                            layer.close(index);
                            var icon = result.code==0?6:5;
                            layer.msg(result.msg,{icon:icon})
                        });
                    });
                } else if(layEvent === 'edit'){
                    location.href = '/admin/queue/'+data.id+'/edit';
                } else if(layEvent === 'agent'){
                    location.href = '/admin/queue/'+data.id+'/agent';
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
                        $.post("{{ route('admin.queue.destroy') }}",{_method:'delete',ids:ids},function (result) {
                            if (result.code==0){
                                dataTable.reload()
                            }
                            layer.close(index);
                            var icon = result.code==0?6:5;
                            layer.msg(result.msg,{icon:icon})
                        });
                    })
                }else {
                    layer.msg('请选择删除项',{icon:5})
                }
            })

            //更新配置
            $("#updateXml").click(function () {
                layer.confirm('该操作将更新所有队列信息，确认操作吗？', function(index){
                    $.post("{{ route('admin.queue.updateXml') }}",{_method:'post',_token:'{{csrf_token()}}'},function (result) {
                        var icon = result.code==0?6:5;
                        layer.msg(result.msg,{icon:icon})
                    });
                })
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
        })
    </script>
@endsection