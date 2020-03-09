@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('admin.gateway_outbound.update',['id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('admin.gateway_outbound._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('admin.gateway_outbound._js')
@endsection