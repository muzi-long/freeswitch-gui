@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加拨号计划</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.extension.store')}}" method="post" class="layui-form">
                @include('admin.dialplan.extension._form')
            </form>
        </div>
    </div>
@endsection