@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新节点</h2>
            @include('admin.order._btn')
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('admin.order.nodeStore',['id'=>$model->id])}}" method="post">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">节点</label>
                    <div class="layui-input-inline">
                        <select name="node_id" lay-verify="required" >
                            <option value=""></option>
                            @foreach($nodes as $d)
                            <option value="{{$d->id}}" @if($model->node_id==$d->id) selected @endif >{{$d->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="layui-word-aux layui-form-mid"></div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">备注</label>
                    <div class="layui-input-inline">
                        <textarea name="content" class="layui-textarea" lay-verify="required" ></textarea>
                    </div>
                    <div class="layui-word-aux layui-form-mid"></div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" lay-submit lay-filter="go" class="layui-btn layui-btn-sm">确认</button>
                        <a href="{{route('admin.order')}}" class="layui-btn layui-btn-sm layui-btn-primary">返回</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('admin.order._js')
@endsection
