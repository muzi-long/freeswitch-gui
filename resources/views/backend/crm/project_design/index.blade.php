@extends('backend.base')

@section('content')
    <style>
        .layui-table tbody .layui-table-cell{
            height:50px;
            line-height: 50px;
        }
    </style>
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div class="layui-btn-group">
                    @can('backend.crm.project-design.destroy')
                        <button class="layui-btn layui-btn-sm layui-btn-danger" type="button" id="listDelete">删 除</button>
                    @endcan
                    @can('backend.crm.project-design.create')
                        <a class="layui-btn layui-btn-sm" href="{{ route('backend.crm.project-design.create') }}">添 加</a>
                    @endcan
                        <button class="layui-btn layui-btn-sm" type="button" lay-submit lay-filter="search" >搜索</button>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">商户</label>
                        <div class="layui-input-inline">
                            <select name="merchant_id" >
                                <option value=""></option>
                                @foreach($merchants as $d)
                                    <option value="{{$d->id}}" >{{$d->company_name}}</option>
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
                    @can('backend.crm.project-design.edit')
                    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    @can('backend.crm.project-design.destroy')
                    <a class="layui-btn layui-btn-danger layui-btn-sm " lay-event="del">删除</a>
                    @endcan
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
                ,url: "{{ route('backend.crm.project-design') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    //,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'merchant_id', title: '所属商户',templet: function (d) {
                            return d.merchant.company_name;
                        }}
                    ,{field: 'field_label', title: '字段名称'}
                    ,{field: 'field_key', title: '字段Key'}
                    ,{field: 'field_type_name', title: '字段类型'}
                    ,{field: 'field_option', title: '字段配置项'}
                    ,{field: 'field_value', title: '默认值'}
                    ,{field: 'sort', title: '排序'}
                    ,{field: 'visiable', title: '可见性',templet:function (d) {
                            return d.visiable==1?'显示':'隐藏';
                        }}
                    ,{field: 'visiable', title: '是否必填',templet:function (d) {
                            return d.required==1?'是':'否';
                        }}
                    ,{field: 'created_at', title: '创建时间'}
                    ,{fixed: 'right', width: 150, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        $.post("{{ route('backend.crm.project-design.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                            if (result.code==0){
                                obj.del(); //删除对应行（tr）的DOM结构
                            }
                            layer.close(index);
                            var icon = result.code==0?1:2;
                            layer.msg(result.msg,{icon:icon})
                        });
                    });
                } else if(layEvent === 'edit'){
                    location.href = '/backend/crm/project-design/'+data.id+'/edit';
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
                        $.post("{{ route('backend.crm.project-design.destroy') }}",{_method:'delete',ids:ids},function (result) {
                            layer.close(index);
                            var icon = result.code==0?1:2;
                            layer.msg(result.msg,{icon:icon},function () {
                                if (result.code==0){
                                    dataTable.reload()
                                }
                            })
                        });
                    })
                }else {
                    layer.msg('请选择删除项',{icon:5})
                }
            })
            //搜索
            form.on('submit(search)',function (data) {
                dataTable.reload({
                    where:data.field,
                    page:{curr: 1}
                })
                return false;
            })

        })
    </script>
@endsection
