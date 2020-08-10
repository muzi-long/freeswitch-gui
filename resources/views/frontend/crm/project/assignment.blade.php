@extends('frontend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div class="layui-btn-group">
                    @can('frontend.crm.assignment.destroy')
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删除</button>
                    @endcan
                    @can('frontend.crm.assignment.import')
                        <button type="button" id="import_project" class="layui-btn layui-btn-sm">导入</button>
                    @endcan
                    @can('frontend.crm.assignment.downloadTemplate')
                        <a href="{{route('frontend.crm.assignment.downloadTemplate')}}" class="layui-btn layui-btn-sm layui-btn-warm">模板下载</a>
                    @endcan

                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">联系人：</label>
                        <div class="layui-input-block" style="width: 275px">
                            <input type="text" name="contact_name" placeholder="请输入姓名" class="layui-input" >
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">联系电话：</label>
                        <div class="layui-input-block" style="width: 275px">
                            <input type="text" name="contact_phone" placeholder="请输入联系电话" class="layui-input" >
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
                @can('frontend.crm.assignment.to')
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label for="" class="layui-form-label">用户：</label>
                            <div class="layui-input-block" style="width: 275px">
                                <select id="user_id">
                                    <option value=""></option>
                                    @foreach($staffs as $user)
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
                ,url: "{{ route('frontend.crm.assignment') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'company_name', title: '公司名称'}
                    ,{field: 'contact_name', title: '联系人'}
                    ,{field: 'contact_phone', title: '联系电话'}
                    ,{field: 'created_at', title: '创建时间'}
                ]]
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
                    ,url: '{{route('frontend.crm.assignment.import')}}'
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
                        $.post("{{ route('frontend.crm.assignment.destroy') }}", {
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
                        $.post("{{ route('frontend.crm.assignment.to') }}", {
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
