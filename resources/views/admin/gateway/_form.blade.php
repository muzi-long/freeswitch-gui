{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">网关名称</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="name" lay-verify="required" value="{{$model->name??old('name')}}" placeholder="如：联通">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">网关地址</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="realm" lay-verify="required" value="{{$model->realm??old('realm')}}" placeholder="格式：192.168.254.100:5066,不加：5066，则默认5060端口">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">帐号</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="username" lay-verify="required" value="{{$model->username??old('username')}}" placeholder="如：Job">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">密码</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="password" lay-verify="required" value="{{$model->password??old('password')}}" placeholder="如：123456">
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit lay-filter="*" >确 认</button>
        <a href="{{route('admin.gateway')}}" class="layui-btn" >返 回</a>
    </div>
</div>