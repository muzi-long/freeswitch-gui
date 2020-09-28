@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('admin.order.follow',['id'=>$order->id])}}" method="post">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">节点</label>
                    <div class="layui-input-inline">
                        <select name="node_id" lay-verify="required" >
                            <option value=""></option>
                            @foreach($nodes as $k=>$v)
                                <option value="{{$k}}" @if($order->node_id==$k) selected @endif >{{$v}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="layui-word-aux layui-form-mid"></div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">下次跟进</label>
                    <div class="layui-input-inline">
                        <input type="text" id="next_follow_at" name="next_follow_at" placeholder="请选择时间" lay-verify="required" readonly class="layui-input">
                    </div>
                    <div class="layui-word-aux layui-form-mid"></div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">备注内容</label>
                    <div class="layui-input-inline">
                        <textarea name="remark" class="layui-textarea" lay-verify="required"></textarea>
                    </div>
                    <div class="layui-word-aux layui-form-mid"></div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" lay-submit lay-filter="go" class="layui-btn layui-btn-sm">确认</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('admin.order._js')
@endsection
