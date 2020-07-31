@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加权限</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('backend.platform.staff_permission.store')}}" method="post">
                @include('backend.platform.staff_permission._from')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('backend.platform.staff_permission._js')
@endsection
