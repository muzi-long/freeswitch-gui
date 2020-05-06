@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新用户</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('admin.user.update',['id'=>$user->id])}}" method="post">
                {{method_field('put')}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">昵称</label>
                    <div class="layui-input-inline">
                        <input type="text" maxlength="16" name="nickname" value="{{ $user->nickname ?? old('nickname') }}" lay-verify="required" placeholder="请输入昵称" class="layui-input" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">电话号码</label>
                    <div class="layui-input-inline">
                        <input type="text" name="phone" value="{{$user->phone??old('phone')}}" lay-verify="required|phone"  placeholder="请输入手机号" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-sm" lay-submit="" lay-filter="go">确 认</button>
                        <a  class="layui-btn layui-btn-sm" href="{{route('admin.user')}}" >返 回</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('admin.user._js')
@endsection

