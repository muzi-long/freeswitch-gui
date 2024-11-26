@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('crm.assignment.import')}}" method="post" class="layui-form">
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">文件</label>
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="uploadBtn">
                            <i class="layui-icon">&#xe67c;</i>点击选择
                        </button>
                        <span id="filename"></span>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <input type="hidden" name="upload_file">
                        <button type="button" class="layui-btn layui-btn-sm layui-disabled" disabled id="importBtn" lay-submit lay-filter="go-close-refresh">确认导入</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer','table','form','laydate','upload'],function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            var laydate = layui.laydate;
            var upload = layui.upload;

            upload.render({
                elem: '#uploadBtn'
                ,url: '{{route('api.upload')}}'
                ,auto: true
                ,multiple: false
                ,accept: 'file'
                ,exts: 'xlsx|xls'
                ,done: function(res){
                    layer.msg(res.msg,{icon:res.code==0?1:2,time:2000},function() {
                        if (res.code==0){
                            $('input[name="upload_file"]').val(res.data.url)
                            $("#filename").text(res.data.url)
                            $("#importBtn").removeClass("layui-disabled")
                            $("#importBtn").removeAttr("disabled")
                        }
                    })
                }
                ,error: function(){
                    layer.msg('上传文件异常',{icon:2});
                }
            });
        })
    </script>
@endsection
