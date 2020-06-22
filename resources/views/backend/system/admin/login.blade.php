<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>后台登录</title>
    <link href="/layuiadmin/layui/css/layui.css" rel="stylesheet">
    <link href="/layuiadmin/backend/login/css/login.css" rel="stylesheet">
</head>
<body>

<div class="login-bg">
    <div class="login-box">
        <h1 class="login-title">
            <center>外呼系统</center>
        </h1>
        <form class="layui-form" action="{{route('backend.system.admin.login')}}"  method="post">
            {{csrf_field()}}
            <div class="layui-form-item field-loginform-username required">
                <label class="layui-form-label" for="loginform-username">帐号</label>
                <div class="layui-input-block">
                    <input type="text" name="username" maxlength="16" lay-verify="required" value="{{old('username')}}" lay-verify="required"
                           placeholder="帐号" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item field-loginform-password required">
                <label class="layui-form-label" for="loginform-password">密码</label>
                <div class="layui-input-block">
                    <input type="password" name="password" maxlength="16" lay-verify="required" placeholder="密码"
                           class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label for="" class="layui-form-label"></label>
                <div class="layui-input-block">
                    <button lay-submit type="submit" class="layui-btn">确定</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/layuiadmin/' //静态资源所在路径
    }).use(['layer', 'form', 'element'], function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;

        //错误提示
        @if(count($errors)>0)
        @foreach($errors->all() as $error)
        layer.msg("{{$error}}",{icon:2});
        @break
        @endforeach
        @endif

    })
</script>
</body>
</html>






