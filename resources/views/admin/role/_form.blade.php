{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">所属</label>
    <div class="layui-input-inline">
        <select name="guard_name">
            <option value="web" @if(isset($role)&&$role->guard_name=='web') selected @endif>后台</option>
            <option value="merchant" @if(isset($role)&&$role->guard_name=='merchant') selected @endif >前台</option>
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" maxlength="16" name="name" lay-verify="required" value="{{$role->name??old('name')}}" placeholder="如:admin">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">显示名称</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" maxlength="16" name="display_name" lay-verify="required" value="{{$role->display_name??old('display_name')}}" placeholder="如：管理员" >
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit="" >确 认</button>
        <a href="{{route('admin.role')}}" class="layui-btn" >返 回</a>
    </div>
</div>