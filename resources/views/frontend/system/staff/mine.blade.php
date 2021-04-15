@extends('frontend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>个人资料</h2>
        </div>
        <div class="layui-card-body">
            <form action="" class="layui-form">
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">姓名</label>
                    <div class="layui-input-inline">
                        <input type="text" name="nickname" value="{{$staff->nickname}}" disabled class="layui-input layui-disabled">
                    </div>
                    <div class="layui-form-mid layui-word-aux">可修改</div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">帐号</label>
                    <div class="layui-input-inline">
                        <input type="text" value="{{$staff->username}}" disabled class="layui-input layui-disabled">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">部门</label>
                    <div class="layui-input-inline">
                        <input type="text" value="{{$staff->department->name}}" disabled class="layui-input layui-disabled">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分机号</label>
                    <div class="layui-input-inline">
                        <input type="text" value="{{$staff->sip->username}}" disabled class="layui-input layui-disabled">
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


