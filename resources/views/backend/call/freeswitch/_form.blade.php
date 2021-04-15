{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" maxlength="16" name="name" lay-verify="required" value="{{$model->name??old('name')}}" placeholder="如:服务器一">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">外网IP</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="external_ip" lay-verify="required" value="{{$model->external_ip??old('external_ip')}}" placeholder="如：112.112.112.112" >
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">内网IP</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="internal_ip" lay-verify="required" value="{{$model->internal_ip??'127.0.0.1'}}" placeholder="如：127.0.0.1" >
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">ESL端口</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="number" name="esl_port" lay-verify="required" value="{{$model->esl_port??8021}}" placeholder="如：8021" >
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">ESL密码</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="esl_password" lay-verify="required" value="{{$model->esl_password??'ClueCon'}}" placeholder="如：ClueCon" >
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">注册端口</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="number" name="internal_sip_port" lay-verify="required" value="{{$model->internal_sip_port??5060}}" placeholder="如：5060" >
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">HTTP端口</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="number" name="swoole_http_port" lay-verify="required" value="{{$model->swoole_http_port??9501}}" placeholder="如：9501" >
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">安装目录</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="fs_install_path" lay-verify="required" value="{{$model->fs_install_path??'/usr/local/freeswitch'}}" placeholder="如：/usr/local/freeswitch" >
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">录音目录</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="fs_record_path" lay-verify="required" value="{{$model->fs_record_path??'/www/wwwroot/recordings'}}" placeholder="如：/www/wwwroot/recordings" >
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go" >确 认</button>
        <a href="{{route('backend.call.freeswitch')}}" class="layui-btn layui-btn-sm" >返 回</a>
    </div>
</div>
