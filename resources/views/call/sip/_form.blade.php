{{csrf_field()}}
<div class="layui-form-item">
    <label class="layui-form-label">网关</label>
    <div class="layui-input-block">
        <select name="gateway_id" >
            <option value=""></option>
            @foreach($gateways as $gw)
                <option value="{{$gw->id}}" @if(isset($model)&&$model->gateway_id==$gw->id) selected @endif >{{$gw->name}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">分机号</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="username" lay-verify="required" value="{{$model->username??old('username')}}" placeholder="如：1000">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">密码</label>
    <div class="layui-input-block">
        <input class="layui-input" type="text" name="password" lay-verify="required" value="{{$model->password??old('password')}}" placeholder="如：1234">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label"></label>
    <div class="layui-input-inline">
        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go-close-refresh" >确 认</button>
    </div>
</div>
