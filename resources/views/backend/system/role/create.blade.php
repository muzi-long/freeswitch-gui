@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加角色</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('backend.system.role.store')}}" method="post" class="layui-form">
                @include('backend.system.role._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('backend.system.role._js')
@endsection
