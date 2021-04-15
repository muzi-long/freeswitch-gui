{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="name" lay-verify="required" value="{{$model->name??old('name')}}" placeholder="如：联通">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">计费花费</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="number" name="cost" lay-verify="required" value="{{$model->cost_format??old('cost')}}" placeholder="如：0.15">
    </div>
    <div class="layui-form-mid layui-word-aux">元，只会保留到分，如:0.11111元会自动更改为0.11元</div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">计费周期</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="number" name="time" lay-verify="required" value="{{$model->time??old('time')}}" placeholder="默认：60">
    </div>
    <div class="layui-form-mid layui-word-aux">秒</div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">描述</label>
    <div class="layui-input-inline">
        <textarea name="description" class="layui-textarea"></textarea>
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go" >确 认</button>
        <a href="{{route('backend.call.rate')}}" class="layui-btn layui-btn-sm" >返 回</a>
    </div>
</div>
