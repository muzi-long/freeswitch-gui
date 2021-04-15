@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加员工</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('backend.platform.staff.store')}}" method="post" class="layui-form">
                @include('backend.platform.staff._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('backend.platform.staff._js')
@endsection
