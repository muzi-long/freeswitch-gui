{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="display_name" lay-verify="required" value="{{$model->display_name??old('display_name')}}" placeholder="如：内线呼内线">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">标识符</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="name" lay-verify="required" value="{{$model->name??old('name')}}" placeholder="如：local_extension">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">类型</label>
    <div class="layui-input-block">
        <input type="radio" name="context" value="default" title="呼出" @if(!isset($model->context) || $model->context=="default") checked @endif>
        <input type="radio" name="context" value="public" title="呼入" @if(isset($model->context)&&$model->context=="public") checked @endif>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">continue</label>
    <div class="layui-input-block">
        <input type="radio" name="continue" value="false" title="false" @if(!isset($model->continue) || $model->continue=="false") checked @endif>
        <input type="radio" name="continue" value="true" title="true" @if(isset($model->continue)&&$model->continue=="true") checked @endif >
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
        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go-close-refresh" >确认</button>
    </div>
</div>
