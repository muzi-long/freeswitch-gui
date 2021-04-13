@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('callcenter.queue.store')}}" method="post" class="layui-form">
                @include('callcenter.queue._form')
            </form>
        </div>
    </div>
@endsection

