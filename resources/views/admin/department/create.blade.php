@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加部门</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.department.store')}}" method="post" class="layui-form">
                @include('admin.department._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('admin.department._js')
@endsection