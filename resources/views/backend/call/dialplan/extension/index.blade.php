@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form" action="{{route('backend.call.extension.updateXml')}}">
                <div class="layui-btn-group">
                    @can('backend.call.extension.destroy')
                    <button class="layui-btn layui-btn-sm layui-btn-danger" type="button" id="listDelete">删 除</button>
                    @endcan
                    @can('backend.call.extension.create')
                    <a class="layui-btn layui-btn-sm" href="{{ route('backend.call.extension.create') }}">添 加</a>
                    @endcan
                    @can('backend.call.extension.updateXml')
                    <button class="layui-btn layui-btn-sm" type="button" lay-submit lay-filter="go" >更新配置</button>
                    @endcan
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">FS服务</label>
                    <div class="layui-input-inline">
                        <select name="fs_id" lay-verify="required">
                            <option value=""></option>
                            @foreach($fs as $d)
                                <option value="{{$d->id}}">{{$d->name}}({{$d->external_ip}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('backend.call.extension.show')
                    <a class="layui-btn layui-btn-sm" lay-event="show">详情</a>
                    @endcan
                    @can('backend.call.extension.edit')
                    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    <a class="layui-btn layui-btn-sm" lay-event="condition">拨号规则</a>
                    @can('backend.call.extension.destroy')
                    <a class="layui-btn layui-btn-danger layui-btn-sm " lay-event="del">删除</a>
                    @endcan
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
                ,url: "{{ route('backend.call.extension') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'display_name', title: '名称'}
                    ,{field: 'name', title: '标识符'}
                    ,{field: 'context_name', title: '类型'}
                    ,{field: 'continue', title: 'continue'}
                    ,{field: 'sort', title: '序号',width:80}
                    ,{field: 'created_at', title: '添加时间',width:170}
                    ,{fixed: 'right', width: 260, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        $.post("{{ route('backend.call.extension.destroy') }}",{_method:'delete',ids:[data.id]},function (res) {
                            if (res.code==0){
                                obj.del(); //删除对应行（tr）的DOM结构
                            }
                            layer.close(index);
                            var icon = res.code==0?1:2;
                            layer.msg(res.msg,{icon:icon})
                        });
                    });
                } else if(layEvent === 'edit'){
                    location.href = '/backend/call/extension/'+data.id+'/edit';
                } else if(layEvent === 'condition'){
                    newTab('/backend/call/extension/'+data.id+'/condition',data.display_name+' - 拨号规则');
                } else if(layEvent === 'show'){
                    newTab('/backend/call/extension/'+data.id+'/show',data.display_name+' - 详情');
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
                        $.post("{{ route('backend.call.extension.destroy') }}",{_method:'delete',ids:ids},function (res) {
                            if (res.code==0){
                                dataTable.reload()
                            }
                            layer.close(index);
                            var icon = res.code==0?1:2;
                            layer.msg(res.msg,{icon:icon})
                        });
                    })
                }else {
                    layer.msg('请选择删除项',{icon:5})
                }
            })

        })
    </script>
@endsection
