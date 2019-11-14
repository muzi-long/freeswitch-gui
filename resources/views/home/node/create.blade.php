@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加节点</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('home.node.store')}}" method="post" class="layui-form">
                @include('home.node._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer','table','form','element'],function () {
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            var element = layui.element;

        });
    </script>
@endsection