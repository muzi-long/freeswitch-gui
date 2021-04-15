<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>后台登录</title>
    <link href="/layuiadmin/layui/css/layui.css" rel="stylesheet">
    <link href="/layuiadmin/backend/login/css/admin.css" rel="stylesheet">
    <link href="/layuiadmin/backend/login/css/login.css" rel="stylesheet">
</head>
<body>

<div class="layadmin-user-login layadmin-user-display-show">
    <div class="layadmin-user-login-main">
        <div class="layadmin-user-login-box layadmin-user-login-header">
            <h2>后台管理端</h2>
            <p>后台管理系统，仅内部使用</p>
        </div>
        <div class="layadmin-user-login-box layadmin-user-login-body layui-form">
            <form action="{{route('backend.system.admin.login')}}" method="post" class="layui-form">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label class="layadmin-user-login-icon layui-icon layui-icon-username"
                           for="LAY-user-login-username"></label>
                    <input type="text" name="username" maxlength="16" lay-verify="required" value="{{old('username')}}" lay-verify="required"
                           placeholder="用户名" class="layui-input">
                </div>
                <div class="layui-form-item">
                    <label class="layadmin-user-login-icon layui-icon layui-icon-password"
                           for="LAY-user-login-password"></label>
                    <input type="password" name="password" maxlength="16" lay-verify="required" placeholder="密码"
                           class="layui-input">
                </div>
                <div class="layui-form-item">
                    <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="*">登 入</button>
                </div>
            </form>

        </div>
    </div>

    <div class="layui-trans layadmin-user-login-footer">
        <p>© 2016 <a>nicaicai.top</a></p>
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






