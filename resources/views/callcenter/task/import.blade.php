@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('callcenter.task.importCall',['id'=>$model->id])}}" method="post" class="layui-form">
                <div class="layui-form">
                    <div class="layui-form-item">
                        <label for="" class="layui-form-label">文件</label>
                        <div class="layui-input-inline">
                            <button type="button" class="layui-btn layui-btn-sm" id="uploadBtn">
                                <i class="layui-icon">&#xe67c;</i>点击选择
                            </button>
                        </div>
                        <div class="layui-word-aux layui-form-mid" id="tips"></div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <input type="hidden" name="file" id="file">
                        <button type="button" class="layui-btn layui-btn-sm layui-disabled" disabled lay-submit lay-filter="go-close" id="sureBtn" >确认导入</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['jquery','layer','table','form','upload'],function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var upload = layui.upload;

            //普通图片上传
            var uploadInst = upload.render({
                elem: '#uploadBtn'
                ,url: '{{route('api.upload')}}' //改成您自己的上传接口
                ,accept: 'file'
                ,exts: 'xlsx'
                ,before: function(obj){
                    layer.load()
                }
                ,done: function(res){
                    layer.closeAll('loading')
                    layer.msg(res.msg,{},function () {
                        if (res.code===0){
                            $("#tips").text(res.data.url)
                            $("#file").val(res.data.url)
                            $("#sureBtn").removeAttr('disabled')
                            $("#sureBtn").removeClass('layui-disabled')
                        }
                    })
                }
                ,error: function(){
                    layer.msg('上传错误',{icon:2})
                }
            });
        })
    </script>
@endsection
