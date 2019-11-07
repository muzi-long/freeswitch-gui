@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加权限</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('admin.merchant-permission.store')}}" method="post">
                @include('admin.merchant-permission._from')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('admin.merchant-permission._js')
@endsection