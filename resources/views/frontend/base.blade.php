<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/layuiadmin/style/admin.css" media="all">
</head>
<body>
<div class="layui-fluid">
    @yield('content')
</div>
<script src="/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['element','form','layer','table','upload','laydate','jquery'],function () {
        var $ = layui.jquery;
        var form = layui.form;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        form.on('submit(go)',function (data) {
            var load = layer.load();
            $.post(data.form.action,data.field,function (res) {
                layer.close(load);
                layer.msg(res.msg,{icon:res.code==0?1:2},function () {
                    if (res.code==0 && res.url){
                        location.href = res.url;
                        return;
                    }
                    if (res.code==0 && res.refresh){
                        parent.location.reload();
                        return
                    }
                })
            });
            return false;
        })

        window.newTab = function (url,tit){
            if(top.layui.index){
                top.layui.index.openTabsPage(url,tit)
            }else{
                window.open(url)
            }
        }

        //呼叫
        window.call = function (phone,exten="",user_id="{{auth()->id()}}") {
            layer.confirm('请确认已分配了分机并登录成功？',function(index) {
                layer.close(index);
                var load = layer.load();
                $.post("{{route('api.dial')}}",{exten:exten,phone:phone,user_id:user_id},function(res) {
                    layer.close(load);
                    layer.msg(res.msg,{time:2000})
                });
            });
        }

    });
</script>
@yield('script')
</body>
</html>



