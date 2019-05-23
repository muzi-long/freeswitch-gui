{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">坐席名称</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="name" lay-verify="required" value="{{$model->name??old('name')}}" placeholder="请输入坐席名称">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">分机号</label>
    <div class="layui-input-block">
        <select name="contact" lay-verify="required">
            <option value="">请选择</option>
            @foreach($sips as $sip)
                <option value="{{$sip->username}}" @if(isset($model->contact)&&'user/'.$sip->username==$model->contact) selected @endif >{{$sip->username}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">坐席状态</label>
    <div class="layui-input-block">
        @foreach(config('freeswitch.agent_status') as $k=>$v)
        <input type="radio" name="status" value="{{$k}}" title="{{$v}}" @if((isset($model->status)&&$model->status==$k) || (!isset($model->status)&&$k=='Available') ) checked @endif >
        @endforeach
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">呼叫状态</label>
    <div class="layui-input-block">
        @foreach(config('freeswitch.agent_state') as $k=>$v)
            <input type="radio" name="state" value="{{$k}}" title="{{$v}}" @if((isset($model->state)&&$model->state==$k) || (!isset($model->state)&&$k=='Waiting')) checked @endif >
        @endforeach
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">最大无应答次数</label>
    <div class="layui-input-block">
        <input class="layui-input" type="number" name="max_no_answer" lay-verify="required|number" value="{{$model->max_no_answer??0}}" >
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">通话间隔</label>
    <div class="layui-input-block">
        <input class="layui-input" type="number" name="wrap_up_time" lay-verify="required|number" value="{{$model->wrap_up_time??0}}" >
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">拒接间隔时间</label>
    <div class="layui-input-block">
        <input class="layui-input" type="number" name="reject_delay_time" lay-verify="required|number" value="{{$model->reject_delay_time??0}}" >
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">忙重试间隔时间</label>
    <div class="layui-input-block">
        <input class="layui-input" type="number" name="busy_delay_time" lay-verify="required|number" value="{{$model->busy_delay_time??0}}" >
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">无应答重试间隔</label>
    <div class="layui-input-block">
        <input class="layui-input" type="number" name="no_answer_delay_time" lay-verify="required|number" value="{{$model->no_answer_delay_time??0}}" >
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit lay-filter="*" >确 认</button>
        <a href="{{route('admin.agent')}}" class="layui-btn" >返 回</a>
    </div>
</div>