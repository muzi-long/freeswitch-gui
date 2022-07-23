@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('callcenter.task.update',['id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('callcenter.task._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('callcenter.task._js')
@endsection
