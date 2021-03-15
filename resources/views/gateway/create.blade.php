@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('call.gateway.store')}}" method="post" class="layui-form">
                @include('gateway._form')
            </form>
        </div>
    </div>
@endsection

