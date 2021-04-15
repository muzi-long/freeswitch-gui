@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新网关</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('backend.call.gateway.update',['id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('backend.call.gateway._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('backend.call.gateway._js')
@endsection
