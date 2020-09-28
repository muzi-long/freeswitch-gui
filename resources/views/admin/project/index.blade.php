@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div class="layui-btn-group">
                    @can('crm.project.create')
                    <a class="layui-btn layui-btn-sm" href="{{ route('admin.project.create') }}">添 加</a>
                    @endcan
                    <button lay-submit lay-filter="search" class="layui-btn layui-btn-sm" >搜索</button>
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
                        <label for="" class="layui-form-label">节点：</label>
                        <div class="layui-input-block" style="width: 275px">
                            <select name="node_id">
                                <option value=""></option>
                                @foreach($nodes as $d)
                                <option value="{{$d->id}}" >{{$d->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">创建人：</label>
                        <div class="layui-input-block" style="width: 275px">
                            <select name="created_user_id" lay-search>
                                <option value=""></option>
                                @foreach($merchants as $d)
                                    <option value="{{$d->id}}" >{{$d->nickname}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">跟进日期：</label>
                        <div class="layui-input-inline" style="width: 120px">
                            <input type="text" id="follow_at_start" name="follow_at_start" placeholder="请选择开始日期" class="layui-input" readonly >
                        </div>
                        <div class="layui-form-mid layui-word-aux">-</div>
                        <div class="layui-input-inline" style="width: 120px">
                            <input type="text" id="follow_at_end" name="follow_at_end" placeholder="请选择结束日期" class="layui-input" readonly >
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">下次跟进：</label>
                        <div class="layui-input-inline" style="width: 120px">
                            <input type="text" id="next_follow_at_start" name="next_follow_at_start" placeholder="请选择开始日期" class="layui-input" readonly >
                        </div>
                        <div class="layui-form-mid layui-word-aux">-</div>
                        <div class="layui-input-inline" style="width: 120px">
                            <input type="text" id="next_follow_at_end" name="next_follow_at_end" placeholder="请选择结束日期" class="layui-input" readonly >
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">创建日期：</label>
                        <div class="layui-input-inline" style="width: 120px">
                            <input type="text" id="created_at_start" name="created_at_start" placeholder="请选择开始日期" class="layui-input" readonly >
                        </div>
                        <div class="layui-form-mid layui-word-aux">-</div>
                        <div class="layui-input-inline" style="width: 120px">
                            <input type="text" id="created_at_end" name="created_at_end" placeholder="请选择结束日期" class="layui-input" readonly >
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">跟进人：</label>
                        <div class="layui-input-block" style="width: 275px">
                            <select name="follow_user_id" lay-search>
                                <option value=""></option>
                                @foreach($merchants as $d)
                                    <option value="{{$d->id}}" >{{$d->nickname}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('crm.project.show')
                    <a class="layui-btn layui-btn-sm" lay-event="show">详情</a>
                    @endcan
                    @can('crm.project.edit')
                    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    @can('crm.project.node')
                    <a class="layui-btn layui-btn-sm" lay-event="node">节点</a>
                    @endcan
                    @can('crm.project.remark')
                    <a class="layui-btn layui-btn-sm" lay-event="remark">备注</a>
                    @endcan
                    @can('crm.project.destroy')
                    <a class="layui-btn layui-btn-danger layui-btn-sm " lay-event="del">删除</a>
                    @endcan
                    @{{# if(d.is_end==0){ }}
                        <a class="layui-btn layui-btn-normal layui-btn-sm " lay-event="order">确认成单</a>
                    @{{# }else{ }}
                        <a class="layui-btn layui-btn-primary layui-btn-sm " >已成单</a>
                    @{{# } }}
                </div>
            </script>
        </div>
    </div>
    <script type="text/html" id="call_phone">
        <span style="display: inline-block;width: 80px">@{{d.phone}}</span>
        <i class="layui-icon layui-icon-cellphone-fine" onclick="call('@{{d.phone}}')" title="点击呼叫" style="cursor: pointer"></i>
    </script>
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
                ,url: "{{ route('admin.project.data') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'company_name', title: '公司名称'}
                    ,{field: 'name', title: '姓名'}
                    ,{field: 'phone', title: '联系电话',width:140,toolbar:'#call_phone'}
                    ,{field: 'node_id', title: '当前节点',templet:function (d) {
                            return d.node.name;
                        }}
                    ,{field: 'follow_merchant_id', title: '跟进人',templet:function (d) {
                            return d.follow_user.nickname;
                        }}
                    ,{field: 'follow_at', title: '跟进时间'}
                    ,{field: 'next_follow_at', title: '下次跟进时间'}
                    ,{field: 'created_at', title: '创建时间'}
                    ,{fixed: 'right', width: 350, align:'center', toolbar: '#options', title:'操作'}
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
                        $.post("{{ route('admin.project.destroy') }}",{_method:'delete',ids:[data.id]},function (res) {
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
                } else if(layEvent === 'edit'){
                    location.href = '/admin/project/'+data.id+'/edit';
                } else if(layEvent === 'show'){
                    location.href = '/admin/project/'+data.id+'/show';
                } else if(layEvent === 'node'){
                    location.href = '/admin/project/'+data.id+'/node';
                } else if(layEvent === 'remark'){
                    location.href = '/admin/project/'+data.id+'/remark';
                } else if(layEvent === 'order'){
                    layer.confirm('确认成单后将进入成单库？', function(index){
                        layer.close(index);
                        var load = layer.load();
                        $.post('/admin/project/'+data.id+'/order',function (res) {
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

            laydate.render({elem: '#follow_at_start', type: 'date'})
            laydate.render({elem: '#follow_at_end', type: 'date'})
            laydate.render({elem: '#next_follow_at_start', type: 'date'})
            laydate.render({elem: '#next_follow_at_end',type: 'date'})
            laydate.render({elem: '#created_at_start',type: 'date'})
            laydate.render({elem: '#created_at_end',type: 'date'})

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
                    ,url: '{{route('admin.project.import')}}'
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

        })
    </script>
@endsection
