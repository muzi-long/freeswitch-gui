@extends('admin.login_register.base')

@section('content')
    <div class="layadmin-user-login-box layadmin-user-login-body layui-form">
        <form action="{{route('admin.login')}}" method="post" class="layui-form">
            {{csrf_field()}}
            <div class="layui-form-item">
                <label class="layadmin-user-login-icon layui-icon layui-icon-username" for="LAY-user-login-username"></label>
                <input type="text" name="username" maxlength="16" value="{{old('username')}}" lay-verify="required" placeholder="帐号" class="layui-input">
            </div>
            <div class="layui-form-item">
                <label class="layadmin-user-login-icon layui-icon layui-icon-password" for="LAY-user-login-password"></label>
                <input type="password" name="password" maxlength="16" lay-verify="required" placeholder="密码" class="layui-input">
            </div>
            <div class="layui-form-item">
                <div class="layui-row">
                    <div class="layui-col-xs7">
                        <label class="layadmin-user-login-icon layui-icon layui-icon-vercode" for="LAY-user-login-vercode"></label>
                        <input type="text" name="captcha" maxlength="4" id="LAY-user-login-vercode" lay-verify="required" placeholder="验证码" class="layui-input">
                    </div>
                    <div class="layui-col-xs5">
                        <div style="margin-left: 10px;">
                            <img src="{{captcha_src()}}" class="layadmin-user-login-codeimg" >
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="*">登 入</button>
            </div>
        </form>

    </div>
@endsection