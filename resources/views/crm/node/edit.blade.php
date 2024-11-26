@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('crm.node.update',['id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('crm.node._form')
            </form>
        </div>
    </div>
@endsection
