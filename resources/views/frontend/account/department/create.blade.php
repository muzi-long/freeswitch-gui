@extends('frontend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加部门</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('frontend.account.department.store')}}" method="post" class="layui-form">
                @include('frontend.account.department._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('frontend.account.department._js')
@endsection
