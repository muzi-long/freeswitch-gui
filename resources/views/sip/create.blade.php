@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('call.sip.store')}}" method="post" class="layui-form">
                @include('sip._form')
            </form>
        </div>
    </div>
@endsection
