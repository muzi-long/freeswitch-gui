@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新配置</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.config.update',['id'=>$data->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('admin.config._form')
            </form>
        </div>
    </div>
@endsection