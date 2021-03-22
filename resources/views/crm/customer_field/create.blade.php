@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('crm.customer_field.store')}}" method="post" class="layui-form">
                @include('crm.customer_field._form')
            </form>
        </div>
    </div>
@endsection

