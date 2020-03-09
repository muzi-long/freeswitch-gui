@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">网关</label>
                    <div class="layui-input-inline">
                        <select name="gateway_id" lay-verify="required" >
                            <option value=""></option>
                            @foreach($gateways as $gw)
                            <option value="{{$gw->id}}" >{{$gw->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分机</label>
                    <div class="layui-input-inline">
                        <textarea class="layui-textarea" name="content" lay-verify="required"></textarea>
                    </div>
                    <div class="layui-form-mid layui-word-aux">单个：1000<br/>多个：1000,1001,1002<br/>区间：1001-1005</div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label"></label>
                    <div class="layui-input-inline">
                        <button type="button" class="layui-btn" lay-submit lay-filter="*" >确 认</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer', 'table', 'form','element'], function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            var element = layui.element;

            form.on('submit(*)',function (data) {
                var load = layer.load();
                $.post("{{route('admin.sip.updateGateway')}}",data.field,function (res) {
                    layer.close(load);
                    layer.msg(res.msg,{time:1500},function () {
                        if (res.code==0){
                            parent.location.reload();
                        }
                    })
                });
                return false;
            })

        });
    </script>
@endsection