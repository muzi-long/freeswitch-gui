@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('call.action.store',['condition_id'=>$condition->id])}}" method="post" class="layui-form">
                @include('call.dialplan.action._form')
            </form>
        </div>
    </div>
@endsection


