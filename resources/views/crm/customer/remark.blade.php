@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('crm.customer.remark',['id'=>$customer->id])}}" method="post">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">节点</label>
                    <div class="layui-input-block">
                        @include('common.get_node',['node_id'=>$customer->node_id,'type'=>2])
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">备注内容</label>
                    <div class="layui-input-block">
                        <textarea name="content" class="layui-textarea" lay-verify="required"></textarea>
                    </div>
                    <div class="layui-word-aux layui-form-mid"></div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">下次跟进</label>
                    <div class="layui-input-block">
                        <input type="text" id="next_follow_time" name="next_follow_time" placeholder="请选择时间" lay-verify="required" readonly class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" lay-submit lay-filter="go-close-refresh" class="layui-btn layui-btn-sm">确认</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer','table','form','element','laydate'],function () {
            var $ = layui.jquery;
            var form = layui.form;
            var laydate = layui.laydate;
            laydate.render({
                elem: '#next_follow_time',
                type: 'datetime'
            });

        });
    </script>
@endsection

