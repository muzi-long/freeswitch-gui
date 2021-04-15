@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('system.menu.update',['id'=>$menu->id])}}" method="post">
                {{method_field('put')}}
                @include('menu._from')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('menu._js')
@endsection
