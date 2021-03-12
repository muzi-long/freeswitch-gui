@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('index.changeMyPassword')}}" >
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">原密码</label>
                    <div class="layui-input-inline">
                        <input type="password" name="old_password" lay-verify="required" placeholder="请输入原密码" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">新密码</label>
                    <div class="layui-input-inline">
                        <input type="password" name="password" maxlength="14" lay-verify="required" placeholder="请输入新密码" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">确认密码</label>
                    <div class="layui-input-inline">
                        <input type="password" name="password2" maxlength="14" lay-verify="required" placeholder="请确认新密码" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label"></label>
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go-close">确 认</button>
                    </div>
                </div>
        </form>
        </div>
    </div>
@endsection


