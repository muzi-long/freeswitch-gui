@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                <button class="layui-btn layui-btn-sm layui-btn-danger" type="button" id="listDelete">删 除</button>
                <button class="layui-btn layui-btn-sm layui-btn-danger" type="button" id="setStatus1">停 止</button>
                <button class="layui-btn layui-btn-sm" type="button" id="setStatus2">启 动</button>
                <a class="layui-btn layui-btn-sm" href="{{ route('admin.task.create') }}">添 加</a>
                <a class="layui-btn layui-btn-sm" href="/template/outgoing.csv">模板下载</a>
            </div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    <a class="layui-btn layui-btn-sm" lay-event="import">导入号码</a>
                    <a class="layui-btn layui-btn-sm" lay-event="show">详情</a>
                    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    <a class="layui-btn layui-btn-danger layui-btn-sm " lay-event="del">删除</a>
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
            <script type="text/html" id="import-html">
                <div style="padding:20px">
                    <div class="layui-form">
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">文件</label>
                            <div class="layui-input-block">
                                <button type="button" class="layui-btn layui-btn-normal" id="uploadBtn">
                                    <i class="layui-icon">&#xe67c;</i>点击选择
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" id="importBtn">确认导入</button>
                        </div>
                    </div>
                </div>
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
                ,height: 500
                ,url: "{{ route('admin.task') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'name', title: '名称'}
                    ,{field: 'date', title: '执行日期',width: 200}
                    ,{field: 'time', title: '执行时间',width: 200}
                    ,{field: 'gateway_name', title: '网关'}
                    ,{field: 'queue_name', title: '队列'}
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
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        $.post("{{ route('admin.task.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                            if (result.code==0){
                                obj.del(); //删除对应行（tr）的DOM结构
                            }
                            layer.close(index);
                            var icon = result.code==0?6:5;
                            layer.msg(result.msg,{icon:icon})
                        });
                    });
                } else if(layEvent === 'edit'){
                    location.href = '/admin/task/'+data.id+'/edit';
                } else if(layEvent === 'import'){
                    layer.open({
                        type : 1,
                        title : '导入号码',
                        shadeClose : true,
                        area : ['500px','auto'],
                        content : $("#import-html").html()
                    });
                    upload.render({
                        elem: '#uploadBtn'
                        ,url: '/admin/task/'+data.id+'/importCall'
                        ,auto: false
                        ,multiple: false
                        ,accept: 'file'
                        ,exts: 'csv'
                        ,bindAction: '#importBtn'
                        ,before: function(obj){
                            layer.load();
                        }
                        ,done: function(res){
                            layer.closeAll('loading');
                            layer.msg(res.msg,{},function() {
                                if (res.code==0){
                                    layer.closeAll();
                                    dataTable.reload()
                                }
                            })
                        }
                    });
                } else if(layEvent === 'show'){
                    location.href = '/admin/task/'+data.id+'/show';
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
                        $.post("{{ route('admin.task.destroy') }}",{_method:'delete',ids:ids},function (result) {
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
                        $.post("{{ route('admin.task.setStatus') }}",{ids:ids,status:status},function (result) {
                            if (result.code==0){
                                dataTable.reload()
                            }
                            layer.close(index);
                            var icon = result.code==0?6:5;
                            layer.msg(result.msg,{icon:icon})
                        });
                    })
                }else {
                    layer.msg('请选择操作项',{icon:5})
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