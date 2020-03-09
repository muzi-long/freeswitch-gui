{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">队列名称</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="display_name" lay-verify="required" value="{{$model->display_name??old('display_name')}}" placeholder="如：队列一">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">振铃策略</label>
    <div class="layui-input-inline">
        <select name="strategy" >
            @foreach(config('freeswitch.strategy') as $k => $v)
                <option value="{{$k}}">{{$v}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">超时时间</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" maxlength="4" name="max_wait_time" lay-verify="required|number" value="{{$model->max_wait_time??0}}" placeholder="">
    </div>
    <div class="layui-form-mid layui-word-aux">最大等待时间，默认0为禁用</div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit lay-filter="*" >确 认</button>
        <a href="{{route('admin.queue')}}" class="layui-btn" >返 回</a>
    </div>
</div>