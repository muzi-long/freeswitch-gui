{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">队列名称</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="name" lay-verify="required" value="{{$model->name??old('name')}}" placeholder="如：队列一">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">振铃策略</label>
    <div class="layui-input-block">
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
    <div class="layui-form-mid layui-word-aux">最大等待时间，0为一直等待</div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">分配坐席</label>
    <div class="layui-input-block">
        @include('common.get_sips_by_queue_id',['queue_id'=>$model->id??0])
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go-close-refresh" >确认</button>
    </div>
</div>
