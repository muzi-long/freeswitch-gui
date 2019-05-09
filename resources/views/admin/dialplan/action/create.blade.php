@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加拨号应用</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.action.store',['condition_id'=>$condition->id])}}" method="post" class="layui-form">
                @include('admin.dialplan.action._form')
            </form>
        </div>
    </div>
@endsection