@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('crm.assignment.store')}}" method="post" class="layui-form">
                @include('common.customer_create',['fields'=>$fields])
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('common.customer_js')
@endsection
