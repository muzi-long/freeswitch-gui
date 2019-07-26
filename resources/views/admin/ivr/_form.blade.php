{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">显示名称</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="display_name" lay-verify="required" value="{{$model->display_name??old('display_name')}}" placeholder="请输入名称">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">标识</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="name" lay-verify="required" value="{{$model->name??old('name')}}" placeholder="请输入唯一标识，如demo1">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">欢迎音</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="greet_long" lay-verify="required" value="{{$model->greet_long??old('greet_long')}}" placeholder="路径或者在线合成">
    </div>
    <div class="layui-word-aux layui-form-mid">首次进入语音导航的欢迎音</div>
    <div class="layui-input-inline">
        <button type="button" class="layui-btn tts-btn">在线合成</button>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">简短提示</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="greet_short" lay-verify="required" value="{{$model->greet_short??old('greet_short')}}" placeholder="路径或者在线合成">
    </div>
    <div class="layui-word-aux layui-form-mid">用户长时间没有按键时提示</div>
    <div class="layui-input-inline">
        <button type="button" class="layui-btn tts-btn">在线合成</button>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">超时时间</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="timeout" lay-verify="required" value="{{$model->timeout??10000}}" placeholder="">
    </div>
    <div class="layui-form-mid layui-word-aux">多长时间没有收到按键就超时（毫秒）</div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">按键间隔</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="number" name="inter_digit_timeout" lay-verify="required|number" value="{{$model->inter_digit_timeout??2000}}" placeholder="">
    </div>
    <div class="layui-form-mid layui-word-aux">两次按键的最大间隔（毫秒）</div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">错误次数</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="number" name="max_failures" lay-verify="required|number" value="{{$model->max_failures??3}}" placeholder="">
    </div>
    <div class="layui-form-mid layui-word-aux">用户按键错误的次数</div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">超时次数</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="number" name="max_timeouts" lay-verify="required|number" value="{{$model->max_timeouts??3}}" placeholder="">
    </div>
    <div class="layui-form-mid layui-word-aux">挂机间隔时间，来电拒接后多久才会有电话进入的等待时长</div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">按键位数</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="number" name="digit_len" lay-verify="required|number" value="{{$model->digit_len??4}}" placeholder="">
    </div>
    <div class="layui-form-mid layui-word-aux">菜单项的长度，即最大收号位数</div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit lay-filter="*" >确 认</button>
        <a href="{{route('admin.ivr')}}" class="layui-btn" >返 回</a>
    </div>
</div>