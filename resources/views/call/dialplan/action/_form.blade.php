{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="display_name" lay-verify="required" value="{{$model->display_name??old('display_name')}}" placeholder="如：挂断">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">应用</label>
    <div class="layui-input-block">
        <select name="application">
            <option ></option>
            @foreach(config('freeswitch.application') as $key => $val)
                <option value="{{$key}}" @if(isset($model->application)&&$model->application==$key) selected @endif >{{$val}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">数据</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="data" value="{{$model->data??old('data')}}" placeholder="某些应用不需要数据可留空">
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
