@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form">
				<div class="layui-form-item">
				    <label for="" class="layui-form-label">网关</label>
				    <div class="layui-input-inline">
				        <select name="gateway_id" lay-verify="required" id="gateway_id" >
				            <option value=""></option>
				            @foreach($gateways as $d1)
				                <option value="{{$d1->id}}" >{{$d1->name}}</option>
				            @endforeach
				        </select>
				    </div>
				</div>
				<div class="layui-form-item">
                    <label for="" class="layui-form-label">文件</label>
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-sm" id="uploadBtn">
                            <i class="layui-icon">&#xe67c;</i>点击选择
                        </button>
                    </div>
                </div>
				<div class="layui-form-item">
				    <div class="layui-input-block">
				        <button type="button" class="layui-btn" type="button" id="importBtn" >确 认</button>
				    </div>
				</div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
	    layui.use(['layer','table','form','upload'],function () {
	        var layer = layui.layer;
	        var form = layui.form;
	        var table = layui.table;
	        var upload = layui.upload;

	        form.on('submit(*)',function (data) {
	            var load = layer.load();
	            $.post(data.form.action,data.field,function (res) {
	                layer.close(load);
	                layer.msg(res.msg,{time:2000},function () {
	                    if (res.code==0){
	                        parent.location.reload();
	                    }
	                })
	            });
	            return false;
	        })

	        upload.render({
                elem: '#uploadBtn'
                ,url: '/admin/gateway_outbound/import'
                ,auto: false
                ,multiple: false
                ,accept: 'file'
                ,exts: 'csv'
                ,bindAction: '#importBtn'
                ,data:{
                	gateway_id:function(){
                		return $("#gateway_id").val();
                	}
                }
                ,before: function(obj){
                    layer.load();
                }
                ,done: function(res){
                    layer.closeAll('loading');
                    layer.msg(res.msg,{},function() {
                        if (res.code==0){
                            layer.closeAll();
                            parent.location.reload();
                        }
                    })
                }
            });
	    });
	</script>
@endsection