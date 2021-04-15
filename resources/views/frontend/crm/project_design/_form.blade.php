{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">字段名称</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="field_label" lay-verify="required" value="{{$model->field_label??old('field_label')}}" placeholder="如：姓名">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">字段Key</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="field_key" lay-verify="required" value="{{$model->field_key??old('field_key')}}" placeholder="如：name">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">字段类型</label>
    <div class="layui-input-inline">
        <select name="field_type" lay-verify="required">
            <option value=""></option>
            @foreach(config('freeswitch.field_type') as $k => $v)
                <option value="{{$k}}" @if(isset($model)&&$model->field_type==$k) selected @endif >{{$v}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">默认值</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="field_value" value="{{$model->field_value??''}}" placeholder="可为空">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">字段选项</label>
    <div class="layui-input-inline">
        <textarea name="field_option" class="layui-textarea">{{$model->field_option??''}}</textarea>
    </div>
    <div class="layui-word-aux layui-form-mid">例：<br/>1:男|2:女|3:保密</div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">排序</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="number" name="sort" lay-verify="required" value="{{$model->sort??10}}" placeholder="序号">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">字段提示</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="field_tips" value="{{$model->field_tips??''}}" placeholder="如：请输入您的姓名">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">可见性</label>
    <div class="layui-input-inline">
        <input type="checkbox" name="visiable" lay-text="显示|隐藏" @if(isset($model)&&$model->visiable==1) checked @endif value="1" lay-skin="switch" lay-filter="visiable">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">是否必填</label>
    <div class="layui-input-inline">
        <input type="checkbox" name="required" lay-text="是|否" @if(isset($model)&&$model->required==1) checked @endif value="1" lay-skin="switch" lay-filter="required">
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go" >确 认</button>
        <a href="{{route('frontend.crm.project-design')}}" class="layui-btn  layui-btn-sm" >返 回</a>
    </div>
</div>
