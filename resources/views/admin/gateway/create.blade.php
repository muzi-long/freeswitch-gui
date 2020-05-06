@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加网关</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.gateway.store')}}" method="post" class="layui-form">
                @include('admin.gateway._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('admin.user._js')
@endsection