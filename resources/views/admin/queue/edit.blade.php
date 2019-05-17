@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新队列</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.queue.update',['id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('admin.queue._form')
            </form>
        </div>
    </div>
@endsection