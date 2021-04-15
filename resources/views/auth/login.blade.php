<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>后台登录</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/layuiadmin/style/admin.css" media="all">
    <link rel="stylesheet" href="/layuiadmin/style/login.css" media="all">
</head>
<body>
<div class="layadmin-user-login layadmin-user-display-show">
    <div class="layadmin-user-login-main">
        <div class="layadmin-user-login-box layadmin-user-login-header">
            <h2>后台管理端</h2>
            <p>后台管理系统，仅内部使用</p>
        </div>
        <div class="layadmin-user-login-box layadmin-user-login-body layui-form">
            <form action="{{route('auth.login')}}" method="post" class="layui-form">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label class="layadmin-user-login-icon layui-icon layui-icon-username"
                           for="LAY-user-login-username"></label>
                    <input type="text" name="name" maxlength="16" value="{{old('name')}}" lay-verify="required"
                           placeholder="帐号" class="layui-input">
                </div>
                <div class="layui-form-item">
                    <label class="layadmin-user-login-icon layui-icon layui-icon-password"
                           for="LAY-user-login-password"></label>
                    <input type="password" name="password" maxlength="16" lay-verify="required" placeholder="密码"
                           class="layui-input">
                </div>
                {{--<div class="layui-form-item">
                    <div class="layui-row">
                        <div class="layui-col-xs7">
                            <label class="layadmin-user-login-icon layui-icon layui-icon-vercode"
                                   for="LAY-user-login-vercode"></label>
                            <input type="text" name="captcha" maxlength="4" id="LAY-user-login-vercode"
                                   lay-verify="required" placeholder="验证码" class="layui-input">
                        </div>
                        <div class="layui-col-xs5">
                            <div style="margin-left: 10px;">
                                <img src="{{captcha_src()}}" id="captcha_img" onclick="this.src=this.src+'?t='+Math.random()" class="layadmin-user-login-codeimg">
                            </div>
                        </div>
                    </div>
                </div>--}}
                <div class="layui-form-item">
                    <button type="button" class="layui-btn layui-btn-fluid" lay-submit lay-filter="go" data-url="/">登 入</button>
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
        form.on('submit(go)', function (data) {
            var url = $(data.elem).data('url')
            $.post(data.form.action, data.field, function (res) {
                var code = res.code
                layer.msg(res.msg, {time: 2000, icon: code == 0 ? 1 : 2}, function () {
                    if (code === 0) {
                        if(url != undefined){
                            location.href = url
                        }
                    }
                });
            }, 'json');
            return false;
        });
    })
</script>
</body>
</html>
