@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加字段</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.project-design.store')}}" method="post" class="layui-form">
                @include('admin.project_design._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('admin.project_design._js')
@endsection