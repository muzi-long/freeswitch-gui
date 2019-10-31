@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div class="layui-form-item">
                    <textarea name="text" class="layui-textarea" placeholder="请输入要合成的文本"></textarea>
                </div>
                <div class="layui-form-item">
                    <button lay-submit lay-filter="tts" class="layui-btn">合成</button>
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('pbx.audio.destroy')
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
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 500
                ,url: "{{ route('admin.audio.data') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    //,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'text', title: '文本'}
                    ,{field: 'url', title: '地址'}
                    ,{field: 'auf', title: '采样率'}
                    ,{field: 'aue', title: '编码'}
                    ,{field: 'voice_name', title: '发音人'}
                    ,{field: 'speed', title: '语速'}
                    ,{field: 'volume', title: '音量'}
                    ,{field: 'pitch', title: '音高'}
                    ,{field: 'engine_type', title: '引擎'}
                    ,{fixed: 'right', width: 150, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });
            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        $.post("{{ route('admin.audio.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                            if (result.code==0){
                                obj.del(); //删除对应行（tr）的DOM结构
                            }
                            layer.close(index);
                            var icon = result.code==0?6:5;
                            layer.msg(result.msg,{icon:icon})
                        });
                    });
                }
            });
            //提交
            form.on('submit(tts)', function (data) {
                layer.load();
                parms = data.field;
                parms['_token'] = "{{csrf_token()}}";
                $.post("{{route('admin.audio.store')}}",parms,function (res) {
                    layer.closeAll('loading');
                    if (res.code==0){
                        layer.msg(res.msg,{icon:6},function () {
                            dataTable.reload({
                                page:{curr:1}
                            });
                        })
                    } else {
                        layer.msg(res.msg,{icon:5})
                    }
                });
                return false;
            });

        })
    </script>
@endsection