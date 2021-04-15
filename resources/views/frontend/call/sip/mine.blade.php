@extends('frontend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>我的分机</h2>
        </div>
        <div class="layui-card-body">
            <form action="" class="layui-form">
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分机号</label>
                    <div class="layui-input-inline">
                        <input type="text" value="{{$sip->username}}" disabled class="layui-input layui-disabled">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">密码</label>
                    <div class="layui-input-inline">
                        <input type="text" name="password" value="{{$sip->password}}" class="layui-input">
                    </div>
                    <div class="layui-form-mid layui-word-aux">可修改</div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">地址</label>
                    <div class="layui-input-inline">
                        <input type="text" value="{{$sip->freeswitch->external_ip}}:{{$sip->freeswitch->internal_sip_port}}" disabled class="layui-input layui-disabled">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">注册时间</label>
                    <div class="layui-input-inline">
                        <input type="text" value="{{$sip->last_register_time}}" disabled class="layui-input layui-disabled">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">注销时间</label>
                    <div class="layui-input-inline">
                        <input type="text" value="{{$sip->last_unregister_time}}" disabled class="layui-input layui-disabled">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go" >确 认</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection


