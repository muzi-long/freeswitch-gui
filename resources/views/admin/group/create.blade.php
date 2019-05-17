@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加分机组</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.group.store')}}" method="post" class="layui-form">
                @include('admin.group._form')
            </form>
        </div>
    </div>
@endsection