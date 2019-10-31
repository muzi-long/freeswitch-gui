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
    <div class="layui-input-inline">
        <input type="radio" name="continue" value="false" title="false" @if(!isset($model->continue) || $model->continue=="false") checked @endif>
        <input type="radio" name="continue" value="true" title="true" @if(isset($model->continue)&&$model->continue=="true") checked @endif >
    </div>
    <div class="layui-form-mid layui-word-aux">true:表示不管该extension中是否有condition匹配，都继续执行dialplan。false：表示如果该extension中有匹配的condition，那么就停止了dialplan</div>
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
        <a href="{{route('admin.extension')}}" class="layui-btn" >返 回</a>
    </div>
</div>