@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-btn-group">
                @can('pbx.ivr.create')
                <a class="layui-btn layui-btn-sm" href="{{ route('admin.ivr.create') }}">添 加</a>
                @endcan
                @can('pbx.ivr.updateXml')
                <button class="layui-btn layui-btn-sm" id="updateXml" >更新配置</button>
                @endcan
            </div>

        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    <a class="layui-btn layui-btn-sm" lay-event="digits">按键</a>
                    @can('pbx.ivr.edit')
                    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    @can('pbx.ivr.destroy')
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
                ,url: "{{ route('admin.ivr.data') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    //,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'display_name', title: '名称'}
                    ,{field: 'name', title: '标识'}
                    ,{field: 'greet_long', title: '欢迎音'}
                    ,{field: 'greet_short', title: '简短提示'}
                    ,{field: 'timeout', title: '超时时间（毫秒）'}
                    ,{field: 'inter_digit_timeout', title: '按键间隔（毫秒）'}
                    ,{field: 'max_failures', title: '错误次数'}
                    ,{field: 'max_timeouts', title: '超时次数'}
                    ,{field: 'digit_len', title: '按键位数'}
                    ,{fixed: 'right', width: 150, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        $.post("{{ route('admin.ivr.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                            if (result.code==0){
                                obj.del(); //删除对应行（tr）的DOM结构
                            }
                            layer.close(index);
                            var icon = result.code==0?6:5;
                            layer.msg(result.msg,{icon:icon})
                        });
                    });
                } else if(layEvent === 'edit'){
                    location.href = '/admin/ivr/'+data.id+'/edit';
                } else if(layEvent === 'digits'){
                    layer.open({
                        type:2,
                        title:'IVR按键菜单',
                        area:['80%','80%'],
                        shadeClose:true,
                        content:"/admin/digits?ivr_id="+data.id
                    })
                }
            });
            //更新配置
            $("#updateXml").click(function () {
                layer.confirm('该操作将重新配置所有IVR，确认操作吗？', function(index){
                    $.post("{{ route('admin.ivr.updateXml') }}",{_method:'post',_token:'{{csrf_token()}}'},function (result) {
                        var icon = result.code==0?6:5;
                        layer.msg(result.msg,{icon:icon})
                    });
                })
            })
        })
    </script>
@endsection