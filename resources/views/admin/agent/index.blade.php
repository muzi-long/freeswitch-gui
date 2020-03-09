@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div class="layui-btn-group">
                    <button class="layui-btn layui-btn-sm layui-btn-danger" type="button" id="listDelete">删 除</button>
                    <a class="layui-btn layui-btn-sm" href="{{ route('admin.agent.create') }}">添 加</a>
                    <button class="layui-btn layui-btn-sm" lay-submit lay-filter="search" >搜 索</button>
                    <button class="layui-btn layui-btn-sm" type="button" onclick="agent_check(1)" >签 入</button>
                    <button class="layui-btn layui-btn-sm" type="button" onclick="agent_check(0)" >签 出</button>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">坐席名称</label>
                        <div class="layui-input-inline">
                            <input type="text" name="display_name" placeholder="" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">呼叫号码</label>
                        <div class="layui-input-inline">
                            <input type="text" name="originate_number" placeholder="" class="layui-input">
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
                    <a class="layui-btn layui-btn-danger layui-btn-sm " lay-event="del">删除</a>
                </div>
            </script>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer','table','form','query'],function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 500
                ,url: "{{ route('admin.agent.data') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true}
                    ,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'display_name', title: '坐席名称'}
                    ,{field: 'originate_type_name', title: '呼叫类型'}
                    ,{field: 'originate_number', title: '呼叫号码'}
                    ,{field: 'status_name', title: '坐席状态'}
                    ,{field: 'state_name', title: '呼叫状态'}
                    ,{field: 'max_no_answer', title: '无应答数'}
                    ,{field: 'wrap_up_time', title: '通话间隔'}
                    ,{field: 'reject_delay_time', title: '挂机间隔'}
                    ,{field: 'busy_delay_time', title: '繁忙间隔'}
                    ,{field: 'no_answer_delay_time', title: '未接间隔'}
                    ,{ width: 150, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        $.post("{{ route('admin.agent.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                            if (result.code==0){
                                obj.del(); //删除对应行（tr）的DOM结构
                            }
                            layer.close(index);
                            var icon = result.code==0?6:5;
                            layer.msg(result.msg,{icon:icon})
                        });
                    });
                } else if(layEvent === 'edit'){
                    location.href = '/admin/agent/'+data.id+'/edit';
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
                        $.post("{{ route('admin.agent.destroy') }}",{_method:'delete',ids:ids},function (result) {
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

            //搜索
            form.on('submit(search)', function(data){
                var parms = data.field;
                dataTable.reload({
                    where:parms,
                    page:{curr:1}
                });
                return false;
            });

            //坐席签入、签出
            window.agent_check = function(status){
                var ids = []
                var hasCheck = table.checkStatus('dataTable')
                var hasCheckData = hasCheck.data
                if (hasCheckData.length>0){
                    $.each(hasCheckData,function (index,element) {
                        ids.push(element.id)
                    })
                }
                if (ids.length>0){
                    var tips = status==1?'签入':'签出';
                    layer.confirm('确认'+tips+'吗？', function(index){
                        layer.close(index);
                        var load = layer.load();
                        $.post("{{ route('admin.agent.check') }}",{ids:ids,status:status},function (result) {
                            layer.close(load);
                            layer.msg(result.msg,{},function(){
                                if (result.code==0){
                                    dataTable.reload()
                                }
                            })
                        });
                    })
                }else {
                    layer.msg('请至少选择一项')
                }
            }

        })
    </script>
@endsection