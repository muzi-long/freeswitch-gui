@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('crm.assignment.update',['id'=>$model->id])}}" method="post" class="layui-form">
                @include('common.customer_edit',['model'=>$model,'fields'=>$fields,'data'=>$data])
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('common.customer_js')
@endsection
