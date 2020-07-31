@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新菜单</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('backend.platform.staff_menu.update',['id'=>$menu->id])}}" method="post">
                {{method_field('put')}}
                @include('backend.platform.staff_menu._from')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('backend.platform.staff_menu._js')
@endsection
