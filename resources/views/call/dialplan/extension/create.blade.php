@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('call.extension.store')}}" method="post" class="layui-form">
                @include('call.dialplan.extension._form')
            </form>
        </div>
    </div>
@endsection


