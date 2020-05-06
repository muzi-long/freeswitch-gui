@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加任务</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.task.store')}}" method="post" class="layui-form">
                @include('admin.task._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('admin.task._js')
@endsection
