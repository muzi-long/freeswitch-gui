@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加分机</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.sip.store')}}" method="post" class="layui-form">
                @include('admin.sip._form')
            </form>
        </div>
    </div>
@endsection