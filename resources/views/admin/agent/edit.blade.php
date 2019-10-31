@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新坐席</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.agent.update',['id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('admin.agent._form')
            </form>
        </div>
    </div>
@endsection