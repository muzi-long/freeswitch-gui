@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新权限</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('backend.platform.staff_permission.update',['id'=>$permission->id])}}" method="post">
                {{method_field('put')}}
                @include('backend.platform.staff_permission._from')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('backend.platform.staff_permission._js')
@endsection
