@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('system.role.update',['id'=>$role->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('system.role._form',['role_id'=>$role->id])
            </form>
        </div>
    </div>
@endsection
