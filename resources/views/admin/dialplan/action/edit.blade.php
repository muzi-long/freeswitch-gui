@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新拨号应用</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.action.update',['condition_id'=>$condition->id,'id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('admin.dialplan.action._form')
            </form>
        </div>
    </div>
@endsection