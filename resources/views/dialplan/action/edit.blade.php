@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('call.action.update',['condition_id'=>$condition->id,'id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('dialplan.action._form')
            </form>
        </div>
    </div>
@endsection


