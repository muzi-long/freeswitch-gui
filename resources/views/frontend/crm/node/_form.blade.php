{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="name" lay-verify="required" value="{{$model->name??''}}" placeholder="请输入名称">
    </div>
    <div class="layui-form-mid layui-word-aux"></div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">排序</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="number" name="sort" lay-verify="required|number" value="{{$model->sort??10}}" placeholder="">
    </div>
    <div class="layui-form-mid layui-word-aux"></div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go" >确 认</button>
        <a href="{{route('frontend.crm.node')}}" class="layui-btn layui-btn-sm" >返 回</a>
    </div>
</div>
