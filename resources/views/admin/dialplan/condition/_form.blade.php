{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="display_name" lay-verify="required" value="{{$model->display_name??old('display_name')}}" placeholder="如：条件一">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">字段</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="field" lay-verify="required" value="{{$model->field??'destination_number'}}" placeholder="默认：destination_number">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">正则</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="expression" lay-verify="required" value="{{$model->expression??old('expression')}}" placeholder="正则表达式">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">break</label>
    <div class="layui-input-block">
        <select name="break">
            <option value="on-false" @if(isset($model->break)&&$model->break=='on-false') selected @endif >on-false</option>
            <option value="on-true" @if(isset($model->break)&&$model->break=='on-true') selected @endif >on-true</option>
            <option value="always" @if(isset($model->break)&&$model->break=='always') selected @endif >always</option>
            <option value="never" @if(isset($model->break)&&$model->break=='never') selected @endif >never</option>
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">序号</label>
    <div class="layui-input-block">
        <input class="layui-input" type="number" name="sort" lay-verify="required" value="{{$model->sort??0}}" >
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit lay-filter="*" >确 认</button>
        <a href="{{route('admin.condition',['extension_id'=>$extension->id])}}" class="layui-btn" >返 回</a>
    </div>
</div>