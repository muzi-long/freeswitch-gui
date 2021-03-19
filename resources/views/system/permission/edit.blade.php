@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('system.permission.update',['id'=>$permission->id])}}" method="post">
                {{method_field('put')}}
                @include('system.permission._from')
            </form>
        </div>
    </div>
@endsection
