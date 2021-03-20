@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('crm.node.store')}}" method="post" class="layui-form">
                @include('crm.node._form')
            </form>
        </div>
    </div>
@endsection
