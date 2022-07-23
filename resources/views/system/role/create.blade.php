@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('system.role.store')}}" method="post" class="layui-form">
                @include('system.role._form')
            </form>
        </div>
    </div>
@endsection
