@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('call.condition.update',['extension_id'=>$extension->id,'id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('dialplan.condition._form')
            </form>
        </div>
    </div>
@endsection


