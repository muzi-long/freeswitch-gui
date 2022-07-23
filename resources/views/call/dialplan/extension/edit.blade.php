@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('call.extension.update',['id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('call.dialplan.extension._form')
            </form>
        </div>
    </div>
@endsection


