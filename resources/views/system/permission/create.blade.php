@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('system.permission.store')}}" method="post">
                @include('system.permission._from')
            </form>
        </div>
    </div>
@endsection
