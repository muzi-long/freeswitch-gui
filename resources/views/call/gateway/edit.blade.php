@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('call.gateway.update',['id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('call.gateway._form')
            </form>
        </div>
    </div>
@endsection

