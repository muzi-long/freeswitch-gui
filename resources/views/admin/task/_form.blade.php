{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="name" lay-verify="required" value="{{$model->name??old('name')}}" placeholder="如：任务一">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">开始时间</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="datetime_start" id="datetime_start" lay-verify="required" value="{{$model->datetime_start??old('datetime_start')}}" readonly placeholder="点击选择时间">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">结束时间</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="datetime_end" id="datetime_end" lay-verify="required" value="{{$model->datetime_end??old('datetime_end')}}" readonly placeholder="点击选择时间">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">网关</label>
    <div class="layui-input-block">
        <select name="gateway_id" lay-verify="required">
            <option value="">请选择</option>
            @foreach($gateways as $gw)
                <option value="{{$gw->id}}" @if((isset($model->gateway_id)&&$model->gateway_id==$gw->id) || (old('gateway_id')==$gw->id)) selected @endif >{{$gw->name}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">队列</label>
    <div class="layui-input-block">
        <select name="queue_id" lay-verify="required">
            <option value="">请选择</option>
            @foreach($queues as $queue)
                <option value="{{$queue->id}}" @if((isset($model->queue_id)&&$model->queue_id==$queue->id) || (old('queue_id')==$queue->id)) selected @endif>{{$queue->display_name}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">最大并发</label>
    <div class="layui-input-block">
        <input class="layui-input" type="number" name="max_channel" lay-verify="required|number" value="{{$model->max_channel??0}}" placeholder="最大并发，默认：0">
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit lay-filter="*" >确 认</button>
        <a href="{{route('admin.task')}}" class="layui-btn" >返 回</a>
    </div>
</div>