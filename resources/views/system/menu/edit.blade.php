@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('system.menu.update',['id'=>$menu->id])}}" method="post">
                {{method_field('put')}}
                @include('system.menu._from')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('system.menu._js')
@endsection
