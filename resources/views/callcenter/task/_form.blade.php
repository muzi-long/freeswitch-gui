{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="name" lay-verify="required" value="{{$model->name??old('name')}}" placeholder="如：任务一">
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-inline">
        <label for="" class="layui-form-label">执行日期</label>
        <div class="layui-input-inline" style="width: 190px;">
            <input class="layui-input" type="text" name="date_start" id="date_start" lay-verify="required" value="{{$model->date_start??old('date_start')}}" readonly placeholder="开始日期">
        </div>
        <div class="layui-form-mid"> - </div>
        <div class="layui-input-inline" style="width: 190px;">
            <input class="layui-input" type="text" name="date_end" id="date_end" lay-verify="required" value="{{$model->date_end??old('date_end')}}" readonly placeholder="结束日期">
        </div>
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-inline">
        <label for="" class="layui-form-label">执行时间</label>
        <div class="layui-input-inline" style="width: 190px;">
            <input class="layui-input" type="text" name="time_start" id="time_start" lay-verify="required" value="{{$model->time_start??old('time_start')}}" readonly placeholder="开始时间">
        </div>
        <div class="layui-form-mid"> - </div>
        <div class="layui-input-inline" style="width: 190px;">
            <input class="layui-input" type="text" name="time_end" id="time_end" lay-verify="required" value="{{$model->time_end??old('time_end')}}" readonly placeholder="结束时间">
        </div>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">网关</label>
    <div class="layui-input-inline">
        <select name="gateway_id" lay-verify="required">
            <option value="">请选择</option>
            @foreach($gateways as $gw)
                <option value="{{$gw->id}}" @if(isset($model->gateway_id)&&$model->gateway_id==$gw->id) selected @endif >{{$gw->name}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">队列</label>
    <div class="layui-input-inline">
        <select name="queue_id" lay-verify="required">
            <option value="">请选择</option>
            @foreach($queues as $queue)
                <option value="{{$queue->id}}" @if(isset($model->queue_id)&&$model->queue_id==$queue->id) selected @endif>{{$queue->name}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">并发数</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="number" name="max_channel" lay-verify="required|number" value="{{$model->max_channel??0}}" placeholder="">
    </div>
    <div class="layui-word-aux layui-form-mid">最大并发，默认：0 为不限制，系统将自动调节</div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn layui-btn-sm" lay-submit lay-filter="go-close-refresh" >确认</button>
    </div>
</div>
