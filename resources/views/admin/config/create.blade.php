@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加配置</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.config.store')}}" method="post" class="layui-form">
                @include('admin.config._form')
            </form>
        </div>
    </div>
@endsection