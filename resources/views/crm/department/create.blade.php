@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('crm.department.store')}}" method="post" class="layui-form">
                @include('crm.department._form')
            </form>
        </div>
    </div>
@endsection
