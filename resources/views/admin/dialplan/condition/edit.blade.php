@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新拨号规则</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.condition.update',['extension_id'=>$extension->id,'id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('admin.dialplan.condition._form')
            </form>
        </div>
    </div>
@endsection