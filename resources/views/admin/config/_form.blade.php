{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">标签</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="label" lay-verify="required" value="{{$data->label??old('label')}}" placeholder="如：标签">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">键</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="key" lay-verify="required" value="{{$data->key??old('key')}}" placeholder="如：key">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">值</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="value" lay-verify="required" value="{{$data->value??old('value')}}" placeholder="如：值" >
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit="" >确 认</button>
        <a href="{{route('admin.config')}}" class="layui-btn" >返 回</a>
    </div>
</div>