@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新角色</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('backend.platform.staff_role.update',['id'=>$role->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('backend.platform.staff_role._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('backend.platform.staff_role._js')
@endsection
