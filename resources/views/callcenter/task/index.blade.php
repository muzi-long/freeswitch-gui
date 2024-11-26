@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                @can('callcenter.task.destroy')
                <button class="layui-btn layui-btn-sm layui-btn-danger" type="button" id="listDelete" data-url="{{route('callcenter.task.destroy')}}">删除</button>
                @endcan
                @can('callcenter.task.setStatus')
                <button class="layui-btn layui-btn-sm layui-btn-danger" type="button" id="setStatus1">停止</button>
                <button class="layui-btn layui-btn-sm" type="button" id="setStatus2">启动</button>
                @endcan
                @can('callcenter.task.create')
                <button class="layui-btn layui-btn-sm" type="button" id="addBtn">添加</button>
                @endcan
                <a class="layui-btn layui-btn-sm" href="/template/calls.xlsx">模板下载</a>
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('callcenter.task.importCall')
                    <a class="layui-btn layui-btn-sm" lay-event="import">导入号码</a>
                    @endcan
                    @can('callcenter.task.show')
                    <a class="layui-btn layui-btn-sm" lay-event="show">详情</a>
                    @endcan
                    @can('callcenter.task.edit')
                    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                </div>
            </script>
            <script type="text/html" id="status">
            @{{# if(d.status==1){ }}
                <span class="layui-badge-dot" style="background-color: red;"></span> 停止
            @{{# } else if(d.status==2){ }}
                <span class="layui-badge-dot" style="background-color: green;"></span> 启动
            @{{# } else if(d.status==3){ }}
                <span class="layui-badge-dot layui-bg-black"></span> 已完成
            @{{# } }}
            </script>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer','table','form','upload','jquery'],function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            var upload = layui.upload;
            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 'full-200'
                ,url: "{{ route('callcenter.task') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'name', title: '名称'}
                    ,{field: 'date', title: '执行日期',width: 200}
                    ,{field: 'time', title: '执行时间',width: 200}
                    ,{field: 'gateway_name', title: '网关'}
                    ,{field: 'queue_name', title: '队列'}
                    ,{field: 'calls_count', title: '号码数量'}
                    ,{field: 'max_channel', title: '并发'}
                    ,{field: 'status', title: '状态', toolbar: '#status'}
                    ,{field: 'created_at', title: '添加时间',width: 160}
                    ,{fixed: 'right', width: 240, align:'center', toolbar: '#options', title:'操作'}
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
                        area: ["800px","600px"],
                        content: '/callcenter/task/'+data.id+'/edit',
                    })
                } else if(layEvent === 'import'){
                    layer.open({
                        type : 2,
                        title : '导入号码',
                        shadeClose : true,
                        area : ['600px','300px'],
                        content : "/callcenter/task/"+data.id+"/importCall"
                    });
                } else if(layEvent === 'show'){
                    newTab('/callcenter/task/'+data.id+'/show','任务详情')
                }
            });

            $("#addBtn").click(function () {
                layer.open({
                    type: 2,
                    title: "添加",
                    shadeClose: true,
                    area: ["800px","600px"],
                    content: '/callcenter/task/create',
                })
            })

            function setStatus(msg,status) {
                var ids = []
                var hasCheck = table.checkStatus('dataTable')
                var hasCheckData = hasCheck.data
                if (hasCheckData.length>0){
                    $.each(hasCheckData,function (index,element) {
                        ids.push(element.id)
                    })
                }
                if (ids.length>0){
                    layer.confirm(msg, function(index){
                        $.post("{{ route('callcenter.task.setStatus') }}",{ids:ids,status:status},function (result) {
                            layer.close(index);
                            var icon = result.code===0?1:2;
                            layer.msg(result.msg,{icon:icon},function () {
                                if (result.code===0){
                                    dataTable.reload()
                                }
                            })
                        });
                    })
                }else {
                    layer.msg('请选择操作项',{icon:2})
                }
            }

            //停止
            $("#setStatus1").click(function () {
                setStatus('确认停止吗？',1);
            });
            //启动
            $("#setStatus2").click(function () {
                setStatus('确认启动吗？',2);
            });

        })
    </script>
@endsection
