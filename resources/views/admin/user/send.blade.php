@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body" style="height: 280px">
            <form class="layui-form" action="{{route('admin.order.order',['id'=>$model->id])}}" method="post">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">接单人</label>
                    <div class="layui-input-inline">
                        <select name="accept_user_id" lay-verify="required" >
                            <option value=""></option>
                            @foreach($users as $d)
                                <option value="{{$d->id}}"  >{{$d->nickname}}</option>
                            @endforeach
                        </select>
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
    <script>
        layui.use(['layer','table','form','element','upload','laydate'],function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            var element = layui.element;
            var upload = layui.upload;
            var laydate = layui.laydate;

            form.on('submit(go)',function (data) {
                var load = layer.load();
                $.post(data.form.action,data.field,function (res) {
                    layer.close(load);
                    layer.msg(res.msg,{icon:res.code==0?1:2},function () {
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
