@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('crm.customer_field.update',['id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('crm.customer_field._form')
            </form>
        </div>
    </div>
@endsection
