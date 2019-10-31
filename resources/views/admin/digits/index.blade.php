@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form">
                <div id="digits-box">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label for="" class="layui-form-label">按键</label>
                            <div class="layui-input-inline" style="width: 80px">
                                <input type="number" lay-verify="required|number" class="layui-input digits" placeholder="0-9">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label for="" class="layui-form-label">应用</label>
                            <div class="layui-input-inline">
                                <select class="action" lay-verify="required">
                                    <option value="">请选择</option>
                                    <option value="menu-exec-app">应用</option>
                                    <option value="mmenu-sub">子菜单</option>
                                    <option value="enu-top">父菜单</option>
                                </select>
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label for="" class="layui-form-label">参数</label>
                            <div class="layui-input-inline">
                                <input type="text" class="layui-input param" placeholder="请输入应用参数">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" onclick="digits_add();">增加</button>
                            <button type="button" class="layui-btn layui-btn-primary layui-btn-sm">删除</button>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <button lay-submit lay-filter="digits-submit" class="layui-btn">确认</button>
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('pbx.digits.destroy')
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

            window.digits_add = function() {
                var _html = '<div class="layui-form-item">\n' +
                    '                        <div class="layui-inline">\n' +
                    '                            <label for="" class="layui-form-label">按键</label>\n' +
                    '                            <div class="layui-input-inline" style="width: 80px">\n' +
                    '                                <input type="number" lay-verify="required|number" class="layui-input digits" placeholder="0-9">\n' +
                    '                            </div>\n' +
                    '                        </div>\n' +
                    '                        <div class="layui-inline">\n' +
                    '                            <label for="" class="layui-form-label">应用</label>\n' +
                    '                            <div class="layui-input-inline">\n' +
                    '                                <select class="action" lay-verify="required">\n' +
                    '                                    <option value="">请选择</option>\n' +
                    '                                    <option value="menu-exec-app">应用</option>\n' +
                    '                                    <option value="mmenu-sub">子菜单</option>\n' +
                    '                                    <option value="enu-top">父菜单</option>\n' +
                    '                                </select>\n' +
                    '                            </div>\n' +
                    '                        </div>\n' +
                    '                        <div class="layui-inline">\n' +
                    '                            <label for="" class="layui-form-label">参数</label>\n' +
                    '                            <div class="layui-input-inline">\n' +
                    '                                <input type="text" class="layui-input param" placeholder="请输入应用参数">\n' +
                    '                            </div>\n' +
                    '                        </div>\n' +
                    '                        <div class="layui-inline">\n' +
                    '                            <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" onclick="digits_add();">增加</button>\n' +
                    '                            <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" onclick="digits_del(this);">删除</button>\n' +
                    '                        </div>\n' +
                    '                    </div>'
                $("#digits-box").append(_html);
                form.render();
            }

            window.digits_del = function (obj) {
                $(obj).parent().parent('.layui-form-item').remove();
            }

            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 500
                ,url: "{{ route('admin.digits.data',['ivr_id'=>$ivr_id]) }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    //,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'ivr_name', title: 'IVR名称'}
                    ,{field: 'digits', title: '按键'}
                    ,{field: 'action_name', title: '应用'}
                    ,{field: 'param', title: '参数'}
                    ,{field: 'created_at', title: '添加时间'}
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
            form.on('submit(digits-submit)', function (data) {
                layer.load();
                var parm = [];
                $("#digits-box .layui-form-item").each(function (index,elem) {
                    parm[index] = {
                        "digits":$(elem).find(".digits").val(),
                        "action":$(elem).find(".action").val(),
                        "param":$(elem).find(".param").val(),
                        "ivr_id":"{{$ivr_id}}"
                    }
                })
                if (parm.length<=0){
                    layer.closeAll('loading');
                    return false;
                }

                $.post("{{route('admin.digits.store')}}",{parm:parm,_token:"{{csrf_token()}}"},function (res) {
                    layer.closeAll('loading');
                    if (res.code==0){
                        layer.msg(res.msg,{icon:6},function () {
                            location.reload()
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