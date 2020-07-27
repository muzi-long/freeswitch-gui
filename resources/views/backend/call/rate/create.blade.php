@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加费率</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('backend.call.rate.store')}}" method="post" class="layui-form">
                @include('backend.call.rate._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('backend.call.rate._js')
@endsection
