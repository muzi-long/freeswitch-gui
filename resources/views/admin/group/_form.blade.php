{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="display_name" lay-verify="required" value="{{$model->display_name??old('display_name')}}" placeholder="如：组一">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">标识</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" maxlength="4" name="name" lay-verify="required|number" value="{{$model->name??old('name')}}" placeholder="请输入组号码：6000-6999">
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit lay-filter="*" >确 认</button>
        <a href="{{route('admin.group')}}" class="layui-btn" >返 回</a>
    </div>
</div>