{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">商户</label>
    <div class="layui-input-inline">
        <select name="merchant_id" lay-verify="required">
            <option value=""></option>
            @foreach($merchants as $m)
                <option value="{{$m->id}}" @if(isset($model)&&$model->merchant_id==$m->id) selected @endif >{{$m->username}}（{{$m->info->company_name}}）</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">帐号</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="username" lay-verify="required" value="{{$model->username??old('username')}}" placeholder="商家帐号">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">密码</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="password" name="password" @if(!isset($model)) lay-verify="required" @endif value="{{old('password')}}" placeholder="商家密码">
    </div>
    <div class="layui-word-aux layui-form-mid">不修改则留空</div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">联系人</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="contact_name" lay-verify="required" value="{{$model->contact_name??old('contact_name')}}" placeholder="联系人">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">联系电话</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="contact_phone" lay-verify="required" value="{{$model->contact_phone??old('contact_phone')}}" placeholder="联系电话">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">状态</label>
    <div class="layui-input-inline">
        <select name="status" lay-verify="required">
            <option value="">请选择</option>
            @foreach(config('freeswitch.merchant_status') as $k=>$v)
                <option value="{{$k}}" @if(isset($model)&&$model->status==$k) selected @endif >{{$v}}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit="" >确 认</button>
        <a href="{{route('admin.member')}}" class="layui-btn" >返 回</a>
    </div>
</div>