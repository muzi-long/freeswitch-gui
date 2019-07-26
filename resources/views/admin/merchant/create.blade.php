@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加商户</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.merchant.store')}}" method="post" class="layui-form">
                @include('admin.merchant._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('admin.merchant._js')
@endsection
