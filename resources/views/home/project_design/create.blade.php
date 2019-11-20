@extends('home.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加字段</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('home.project-design.store')}}" method="post" class="layui-form">
                @include('home.project_design._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('home.project_design._js')
@endsection