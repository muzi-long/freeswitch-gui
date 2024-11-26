@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('callcenter.queue.update',['id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('callcenter.queue._form')
            </form>
        </div>
    </div>
@endsection
