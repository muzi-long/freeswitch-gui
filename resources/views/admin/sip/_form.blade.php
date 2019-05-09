{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">分机号</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="username" lay-verify="required" value="{{$model->username??old('username')}}" placeholder="如：1000">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">密码</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="password" lay-verify="required" value="{{$model->password??old('password')}}" placeholder="如：1234">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">外显名称</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="effective_caller_id_name" value="{{$model->effective_caller_id_name??old('effective_caller_id_name')}}" placeholder="如：Job">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">外显号码</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="effective_caller_id_number" value="{{$model->effective_caller_id_number??old('effective_caller_id_number')}}" placeholder="如：1000">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">出局名称</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="outbound_caller_id_name"  value="{{$model->outbound_caller_id_name??old('outbound_caller_id_name')}}" placeholder="可为空">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">出局号码</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="outbound_caller_id_number"  value="{{$model->outbound_caller_id_number??old('outbound_caller_id_number')}}" placeholder="可为空">
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit lay-filter="*" >确 认</button>
        <a href="{{route('admin.sip')}}" class="layui-btn" >返 回</a>
    </div>
</div>