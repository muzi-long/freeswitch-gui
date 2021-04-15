@extends('frontend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新员工</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('frontend.account.staff.update',['id'=>$staff->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('frontend.account.staff._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('frontend.account.staff._js')
@endsection
