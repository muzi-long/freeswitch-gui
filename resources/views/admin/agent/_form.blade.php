{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">坐席名称</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="display_name" lay-verify="required" value="{{$model->display_name??old('display_name')}}" placeholder="请输入坐席名称">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">呼叫类型</label>
    <div class="layui-input-block">
        <input type="radio" name="originate_type" value="user" title="分机" @if( !isset($model->originate_type) || (isset($model->originate_type)&&$model->originate_type=='user') ) checked @endif >
        <input type="radio" name="originate_type" value="group" title="分机组" @if(isset($model->originate_type)&&$model->originate_type=='group') checked @endif >
        <input type="radio" name="originate_type" value="gateway" title="网关" @if(isset($model->originate_type)&&$model->originate_type=='gateway') checked @endif >
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">呼叫号码</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="originate_number" lay-verify="required" value="{{$model->originate_number??old('originate_number')}}" placeholder="">
    </div>
    <div class="layui-form-mid layui-word-aux">分机号码 / 分机组标识 / 网关呼出 ，与类型对应</div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">无应答数</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="max_no_answer" lay-verify="required|number" value="{{$model->max_no_answer??3}}" placeholder="">
    </div>
    <div class="layui-form-mid layui-word-aux">最大无应答次数，超过次数将不再分配话务</div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">通话间隔</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="wrap_up_time" lay-verify="required|number" value="{{$model->wrap_up_time??1}}" placeholder="">
    </div>
    <div class="layui-form-mid layui-word-aux">通话完成间隔时间，成功处理一个通话后，多久才会有电话进入等待时长</div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">挂机间隔</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="reject_delay_time" lay-verify="required|number" value="{{$model->reject_delay_time??1}}" placeholder="">
    </div>
    <div class="layui-form-mid layui-word-aux">挂机间隔时间，来电拒接后多久才会有电话进入的等待时长</div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">繁忙间隔</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="busy_delay_time" lay-verify="required|number" value="{{$model->busy_delay_time??1}}" placeholder="">
    </div>
    <div class="layui-form-mid layui-word-aux">忙重试间隔时间，来电遇忙后多久才会有电话进入的等待时长</div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">未接间隔</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="no_answer_delay_time" lay-verify="required|number" value="{{$model->no_answer_delay_time??1}}" placeholder="">
    </div>
    <div class="layui-form-mid layui-word-aux">无应答重试间隔，来电无应答后多久才会有电话进入的等待时长</div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit lay-filter="*" >确 认</button>
        <a href="{{route('admin.agent')}}" class="layui-btn" >返 回</a>
    </div>
</div>