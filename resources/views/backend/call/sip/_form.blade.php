{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">商户</label>
    <div class="layui-input-inline">
        <select name="merchant_id" lay-verify="required" @if(isset($model)) disabled @endif lay-filter="merchant">
            <option value=""></option>
            @foreach($merchants as $d)
                <option value="{{$d->id}}" {{isset($model)&&$model->merchant_id==$d->id?'selected':''}} >{{$d->company_name}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">网关名称</label>
    <div class="layui-input-inline">
        <select name="gateway_id" lay-verify="required" id="gateway">
            <option value="0"></option>
            @if(isset($gateways))
                @foreach($gateways as $d)
                    <option value="{{$d->id}}" {{isset($model)&&$model->gateway_id==$d->id?'selected':''}} >{{$d->name}}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">费率</label>
    <div class="layui-input-inline">
        <select name="rate_id" >
            <option value="0">无</option>
            @foreach($rates as $d)
                <option value="{{$d->id}}" {{isset($model)&&$model->rate_id==$d->id?'selected':''}} >{{$d->name}}（{{$d->cost_format}}元/{{$d->time}}秒）</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">分机号</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="username" lay-verify="required" value="{{$model->username??old('username')}}" maxlength="4" placeholder="如：1000">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">密码</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="password" lay-verify="required" value="{{$model->password??old('password')}}" placeholder="如：1234">
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go" >确 认</button>
        <a href="{{route('backend.call.sip')}}" class="layui-btn layui-btn-sm" >返 回</a>
    </div>
</div>