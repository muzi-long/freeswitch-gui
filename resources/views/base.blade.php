<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <meta name="renderer" content="webkit">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/layuiadmin/style/admin.css" media="all">
    <script src="/layuiadmin/layui/layui.js"></script>
</head>
<body>
<div class="layui-fluid">
    @yield('content')
</div>

<script src="/layuiadmin/xm-select.js"></script>
<script>
    layui.config({
        base: '/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index', //主入口模块
    }).use(['element', 'form', 'layer', 'table', 'upload', 'laydate', 'jquery'], function () {
        var $ = layui.jquery;
        var form = layui.form;
        var table = layui.table;


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        //表单提交，是否跳转
        form.on('submit(go)', function (data) {
            var url = $(data.elem).data('url')
            var load = layer.load();
            $.post(data.form.action, data.field, function (res) {
                layer.close(load);
                var code = res.code
                layer.msg(res.msg, {time: 2000, icon: code == 0 ? 1 : 2}, function () {
                    if (code === 0 && url != undefined) {
                        location.href = url
                    }
                });
            });
            return false;
        })
        //表单提交，并刷新数据表格，常用于搜索
        form.on('submit(go-reload)', function (data) {
            var load = layer.load();
            $.post(data.form.action, data.field, function (res) {
                layer.close(load);
                var code = res.code
                layer.msg(res.msg, {time: 2000, icon: code == 0 ? 1 : 2}, function () {
                    if (code === 0) {
                        layui.table.reload('dataTable');
                    }
                });
            });
            return false;
        })
        //表单提交并关闭
        form.on('submit(go-close)', function (data) {
            var load = layer.load();
            $.post(data.form.action, data.field, function (res) {
                layer.close(load);
                var code = res.code
                layer.msg(res.msg, {time: 2000, icon: code == 0 ? 1 : 2}, function () {
                    if (code === 0) {
                        parent.layer.close(parent.layer.getFrameIndex(window.name));
                    }
                });
            });
            return false;
        })
        form.on('submit(go-close-refresh)', function (data) {
            var load = layer.load();
            $.post(data.form.action, data.field, function (res) {
                layer.close(load);
                var code = res.code
                layer.msg(res.msg, {time: 2000, icon: code == 0 ? 1 : 2}, function () {
                    if (code === 0) {
                        parent.layui.table.reload('dataTable');
                        parent.layer.close(parent.layer.getFrameIndex(window.name));
                    }
                });
            });
            return false;
        })

        //搜索
        form.on('submit(search)',function(data) {
            layui.table.reload('dataTable',{
                where: data.field,
                page: {curr:1}
            });
            return false;
        });

        window.newTab = function (url, tit) {
            if (top.layui.index) {
                top.layui.index.openTabsPage(url, tit)
            } else {
                window.open(url)
            }
        }

        //呼叫
        window.call = function (phone) {
            var load = layer.load();
            $.post("{{route('api.call')}}",{callee:phone,user_id:"{{auth()->id()}}"},function(res) {
                layer.close(load);
                layer.msg(res.msg,{time:2000})
            });
        }
        //数据表格单行删除
        window.deleteData = function (obj,url) {
            layer.confirm('确认删除吗？', function(index){
                layer.close(index)
                var load = layer.load()
                $.post(url,{_method:'delete',ids:[obj.data.id]},function (res) {
                    layer.close(load);
                    layer.msg(res.msg,{time:1500,icon:res.code==0?1:2},function () {
                        if (res.code==0){
                            obj.del();
                        }
                    })
                });
            });
        }
        //批量删除
        $("#listDelete").click(function () {
            var ids = [];
            var hasCheck = table.checkStatus('dataTable');
            var hasCheckData = hasCheck.data;
            var url = $(this).data('url');
            if (hasCheckData.length > 0) {
                $.each(hasCheckData, function (index, element) {
                    ids.push(element.id)
                })
            }
            if (ids.length > 0) {
                layer.confirm('确认删除吗？', function (index) {
                    layer.close(index);
                    var load = layer.load();
                    $.post(url, {
                        _method: 'delete',
                        ids: ids
                    }, function (res) {
                        layer.close(load);
                        if (res.code == 0) {
                            layer.msg(res.msg, {icon: 1}, function () {
                                table.reload('dataTable');
                            })
                        } else {
                            layer.msg(res.msg, {icon: 2})
                        }
                    });
                })
            } else {
                layer.msg('请选择删除项', {icon: 2})
            }
        })




    });
</script>
@yield('script')
</body>
</html>



