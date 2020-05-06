@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加队列</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.queue.store')}}" method="post" class="layui-form">
                @include('admin.queue._form')
            </form>
        </div>
    </div>
@endsection