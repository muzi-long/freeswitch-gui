@extends('admin.base')

@section('content')
    <div class="layui-card">
        @can('data.audio.create')
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div class="layui-form-item">
                    <textarea name="text" class="layui-textarea" placeholder="请输入要合成的文本"></textarea>
                </div>
                <div class="layui-form-item">
                    <button type="button" lay-submit lay-filter="tts" class="layui-btn">合成</button>
                </div>
            </form>
        </div>
        @endcan
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    <div class="layui-btn-group">
                        <a class="layui-btn layui-btn-sm" lay-event="play">播放</a>
                    </div>
                    @can('data.audio.destroy')
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
                ,url: "{{ route('admin.audio') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    //,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'text', title: '文本'}
                    ,{field: 'url', title: '地址'}
                    ,{field: 'path', title: '完整路径'}
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
                            var icon = result.code==0?1:2;
                            layer.msg(result.msg,{icon:icon})
                        });
                    });
                }else if (layEvent === 'play'){
                    if (data.url) {
                        var _html = '<div style="padding:20px;">';
                        _html += '<audio controls="controls" autoplay src="' + data.url + '"></audio>';
                        _html += '</div>';
                        layer.open({
                            title: '播放录音',
                            type: 1,
                            area: ['360px', 'auto'],
                            content: _html
                        })
                    }
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
                        layer.msg(res.msg,{icon:1},function () {
                            dataTable.reload({
                                page:{curr:1}
                            });
                        })
                    } else {
                        layer.msg(res.msg,{icon:2})
                    }
                });
                return false;
            });

        })
    </script>
@endsection