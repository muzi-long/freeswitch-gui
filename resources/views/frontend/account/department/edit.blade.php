@extends('frontend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新部门</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('frontend.account.department.update',['id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('frontend.account.department._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('frontend.account.department._js')
@endsection
