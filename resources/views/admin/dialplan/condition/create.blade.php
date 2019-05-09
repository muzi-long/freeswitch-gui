@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加拨号规则</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.condition.store',['extension_id'=>$extension->id])}}" method="post" class="layui-form">
                @include('admin.dialplan.condition._form')
            </form>
        </div>
    </div>
@endsection