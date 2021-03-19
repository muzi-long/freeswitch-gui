@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删除</button>
                <a class="layui-btn layui-btn-sm" id="addBtn" >添 加</a>
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
                ,url: "{{ route('call.action',['condition_id'=>$condition->id]) }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'display_name', title: '名称'}
                    ,{field: 'application_name', title: '应用'}
                    ,{field: 'data', title: '数据'}
                    ,{field: 'sort', title: '序号',width:80}
                    ,{field: 'created_at', title: '添加时间',width:170}
                    ,{fixed: 'right', width: 220, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        layer.close(index);
                        var load = layer.load()
                        $.post("{{ route('call.action.destroy') }}",{_method:'delete',ids:[data.id]},function (res) {
                            layer.close(load);
                            layer.msg(res.msg,{time:2000,icon:res.code==0?1:2},function () {
                                if (res.code==0){
                                    obj.del(); //删除对应行（tr）的DOM结构
                                }
                            })
                        });
                    });
                } else if(layEvent === 'edit'){
                    layer.open({
                        type: 2,
                        title: "编辑",
                        shadeClose: true,
                        area: ["800px","600px"],
                        content: '/call/condition/'+data.condition_id+'/action/'+data.id+'/edit',
                        end: function () {
                            dataTable();
                        }
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
                        layer.close(index);
                        var load = layer.load()
                        $.post("{{ route('call.action.destroy') }}",{_method:'delete',ids:ids},function (res) {
                            layer.close(load);
                            layer.msg(res.msg,{time:2000,icon:res.code==0?1:2},function () {
                                if (res.code==0){
                                    dataTable.reload()
                                }
                            })
                        });
                    })
                }else {
                    layer.msg('请选择删除项',{icon:2})
                }
            })
            $("#addBtn").click(function () {
                layer.open({
                    type: 2,
                    title: "添加",
                    shadeClose: true,
                    area: ["800px","600px"],
                    content: "{{route('call.action.create',['condition_id'=>$condition->id])}}",
                    end: function () {
                        dataTable();
                    }
                })
            })
        })
    </script>
@endsection
