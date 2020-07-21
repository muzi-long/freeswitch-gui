@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加分机</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('backend.call.sip.store')}}" method="post" class="layui-form">
                @include('backend.call.sip._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('backend.call.sip._js')
@endsection
