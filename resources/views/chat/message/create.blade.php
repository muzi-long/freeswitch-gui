@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('chat.message.store')}}" method="post" class="layui-form">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">发送给</label>
                    <div class="layui-input-block">
                        @include('common.get_user')
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">标题</label>
                    <div class="layui-input-block">
                        <input class="layui-input" type="text" name="title" lay-verify="required"  placeholder="请输入">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">内容</label>
                    <div class="layui-input-block">
                        <textarea name="content" class="layui-textarea" placeholder="请输入" ></textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go-close-refresh" >确认</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
