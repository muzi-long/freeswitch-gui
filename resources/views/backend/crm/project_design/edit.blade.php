@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新字段</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('backend.crm.project-design.update',['id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                @include('backend.crm.project_design._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('backend.crm.project_design._js')
@endsection
