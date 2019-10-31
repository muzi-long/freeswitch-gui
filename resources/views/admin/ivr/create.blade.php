@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加IVR</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.ivr.store')}}" method="post" class="layui-form">
                @include('admin.ivr._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('admin.ivr._js')
@endsection