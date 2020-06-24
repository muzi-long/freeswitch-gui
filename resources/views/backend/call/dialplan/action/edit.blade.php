@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新拨号应用</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('backend.call.action.update',['condition_id'=>$condition->id,'id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('backend.call.dialplan.action._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer','table','form','jquery'],function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
        })
    </script>
@endsection
