@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加员工</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('home.member.store')}}" method="post" class="layui-form">
                @include('home.member._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['element','form'],function () {

        })
    </script>
@endsection