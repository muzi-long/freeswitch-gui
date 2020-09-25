@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div class="layui-btn-group">
                    @can('crm.assignment.destroy')
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删除</button>
                    @endcan
                    @can('crm.assignment.create')
                        <a href="{{route('admin.assignment.create')}}" class="layui-btn layui-btn-sm" id="listDelete">录入</a>
                    @endcan
                    @can('crm.assignment.import')
                        <button type="button" id="import_project" class="layui-btn layui-btn-sm">导入</button>
                        <a href="{{route('admin.project.downloadTemplate')}}" class="layui-btn layui-btn-sm layui-btn-warm">模板下载</a>
                    @endcan

                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">姓名：</label>
                        <div class="layui-input-block" style="width: 275px">
                            <input type="text" name="name" placeholder="请输入姓名" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">电话：</label>
                        <div class="layui-input-block" style="width: 275px">
                            <input type="text" name="phone" placeholder="请输入联系电话" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">公司名称：</label>
                        <div class="layui-input-block" style="width: 275px">
                            <input type="text" name="company_name" placeholder="请输入公司名称" class="layui-input" >
                        </div>
                    </div>
                    <button type="button" lay-submit lay-filter="search" class="layui-btn layui-btn-sm" >搜索</button>
                </div>
                @can('crm.assignment.to')
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">用户：</label>
                        <div class="layui-input-block" style="width: 275px">
                            <select id="user_id">
                                <option value=""></option>
                                @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->nickname}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="button" class="layui-btn layui-btn-sm" id="assignmentBtn" >分配</button>
                </div>
                @endcan
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('crm.assignment.edit')
                        <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                </div>
            </script>
        </div>
    </div>
    <script type="text/html" id="import-html">
        <div style="padding:20px">
            <div class="layui-form">
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">文件</label>
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="uploadBtn">
                            <i class="layui-icon">&#xe67c;</i>点击选择
                        </button>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn layui-btn-sm" id="importBtn">确认导入</button>
                </div>
            </div>
        </div>
    </script>
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
                ,url: "{{ route('admin.assignment.data') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'company_name', title: '公司名称'}
                    ,{field: 'name', title: '姓名'}
                    ,{field: 'phone', title: '联系电话'}
                    ,{field: 'created_at', title: '创建时间'}
                    ,{field: 'owner_user_id', title: '状态', templet:function (d) {
                            if(d.owner_user_id==0){
                                return '<span class="layui-badge">待分配</span>'
                            }else{
                                return '<span class="layui-badge layui-bg-green">已分配</span>'
                            }
                        }}
                    ,{fixed: 'right', width: 250, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'edit'){
                    location.href = '/admin/assignment/'+data.id+'/edit';
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

            //导入
            $("#import_project").click(function() {
                layer.open({
                    type : 1,
                    title : '导入项目，仅允许xls、xlsx格式',
                    shadeClose : true,
                    area : ['500px','auto'],
                    content : $("#import-html").html()
                })
                upload.render({
                    elem: '#uploadBtn'
                    ,url: '{{route('admin.assignment.import')}}'
                    ,auto: false
                    ,multiple: false
                    ,accept: 'file'
                    ,exts: 'xlsx|xls'
                    ,bindAction: '#importBtn'
                    ,done: function(res){
                        layer.msg(res.msg,{},function() {
                            if (res.code==0){
                                layer.closeAll();
                                dataTable.reload({
                                    page:{curr:1}
                                })
                            }
                        })
                    }
                });
            })

            //批量删除
            $("#listDelete").click(function () {
                var ids = [];
                var hasCheck = table.checkStatus('dataTable');
                var hasCheckData = hasCheck.data;
                if (hasCheckData.length > 0) {
                    $.each(hasCheckData, function (index, element) {
                        ids.push(element.id)
                    })
                }
                if (ids.length > 0) {
                    layer.confirm('确认删除吗？', function (index) {
                        layer.close(index);
                        var load = layer.load();
                        $.post("{{ route('admin.assignment.destroy') }}", {
                            _method: 'delete',
                            ids: ids
                        }, function (res) {
                            layer.close(load);
                            if (res.code == 0) {
                                layer.msg(res.msg, {icon: 1}, function () {
                                    dataTable.reload({page: {curr: 1}});
                                })
                            } else {
                                layer.msg(res.msg, {icon: 2})
                            }
                        });
                    })
                } else {
                    layer.msg('请选择删除项', {icon: 2});
                }
            })

            //分配
            $("#assignmentBtn").click(function () {
                var ids = [];
                var hasCheck = table.checkStatus('dataTable');
                var hasCheckData = hasCheck.data;
                if (hasCheckData.length > 0) {
                    $.each(hasCheckData, function (index, element) {
                        ids.push(element.id)
                    })
                }
                var user_id = $("#user_id").val();
                if(user_id == ''){
                    layer.msg('请选择用户',{icon:2});
                    return false;
                }
                if (ids.length > 0) {
                    layer.confirm('确认分配吗？', function (index) {
                        layer.close(index);
                        var load = layer.load();
                        $.post("{{ route('admin.assignment.to') }}", {
                            ids: ids,
                            user_id:$("#user_id").val()
                        }, function (res) {
                            layer.close(load);
                            if (res.code == 0) {
                                layer.msg(res.msg, {icon: 1}, function () {
                                    dataTable.reload({page: {curr: 1}});
                                })
                            } else {
                                layer.msg(res.msg, {icon: 2})
                            }
                        });
                    })
                } else {
                    layer.msg('请选择分配项', {icon: 2});
                }
            })

        })
    </script>
@endsection
