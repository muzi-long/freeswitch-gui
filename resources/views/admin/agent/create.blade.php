@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加坐席</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.agent.store')}}" method="post" class="layui-form">
                @include('admin.agent._form')
            </form>
        </div>
    </div>
@endsection