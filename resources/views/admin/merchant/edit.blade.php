@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新商家</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.merchant.update',['id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('admin.merchant._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('admin.merchant._js')
@endsection