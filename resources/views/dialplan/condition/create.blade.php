@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('call.condition.store',['extension_id'=>$extension->id])}}" method="post" class="layui-form">
                @include('dialplan.condition._form')
            </form>
        </div>
    </div>
@endsection


