@extends('frontend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新节点</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('frontend.crm.node.update',['id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('frontend.crm.node._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('frontend.crm.node._js')
@endsection
