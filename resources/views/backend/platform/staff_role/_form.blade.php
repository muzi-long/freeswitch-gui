{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">商户</label>
    <div class="layui-input-inline">
        <select name="merchant_id" lay-verify="required">
            <option value=""></option>
            @foreach($merchants as $d)
                <option value="{{$d->id}}" @if(isset($role)&&$role->merchant_id==$d->id) selected @endif >{{$d->company_name}}</option>
            @endforeach
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
        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go" >确 认</button>
        <a href="{{route('backend.system.role')}}" class="layui-btn layui-btn-sm" >返 回</a>
    </div>
</div>
