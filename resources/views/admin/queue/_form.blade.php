{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="display_name" lay-verify="required" value="{{$model->display_name??old('display_name')}}" placeholder="如：队列一">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">标识</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="name" lay-verify="required" value="{{$model->name??old('name')}}" placeholder="如：66666">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">振铃策略</label>
    <div class="layui-input-block">
        <select name="strategy">
            @foreach(config('freeswitch.strategy') as $k=>$v)
                <option value="{{$k}}" @if(isset($model->strategy)&&$model->strategy==$k) selected @endif>{{$v}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit lay-filter="*" >确 认</button>
        <a href="{{route('admin.queue')}}" class="layui-btn" >返 回</a>
    </div>
</div>