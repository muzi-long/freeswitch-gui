{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">所属商户</label>
    <div class="layui-input-inline">
        <select name="merchant_id" >
            <option value="0">无</option>
            @foreach($merchants as $d)
                <option value="{{$d->id}}" {{isset($staff)&&$staff->merchant_id==$d->id?'selected':''}} >{{$d->company_name}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">姓名</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" maxlength="16" name="nickname" lay-verify="required" value="{{$staff->nickname??old('nickname')}}" placeholder="如:张三">
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">帐号</label>
    <div class="layui-input-inline">
        <input class="layui-input" name="username"  type="text" value="{{$staff->username??old('username')}}" placeholder="如：zhangshang" >
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">密码</label>
    <div class="layui-input-inline">
        @if(isset($staff))
            <input class="layui-input layui-disabled" disabled type="password" value="******">
        @else
            <input class="layui-input" type="password" name="password" lay-verify="required"  placeholder="请输入密码" >
        @endif
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">确认密码</label>
    <div class="layui-input-inline">
        @if(isset($staff))
            <input class="layui-input layui-disabled" type="password" disabled value="******" >
        @else
            <input class="layui-input" type="password" name="password_confirmation" lay-verify="required" placeholder="两次密码必需一致" >
        @endif
    </div>
</div>

@if(isset($staff))
    <div class="layui-form-item">
        <label for="" class="layui-form-label">登录时间</label>
        <div class="layui-input-inline">
            <input class="layui-input layui-disabled" type="text" value="{{$staff->last_login_at}}" readonly  >
        </div>
    </div>
    <div class="layui-form-item">
        <label for="" class="layui-form-label">登录IP</label>
        <div class="layui-input-inline">
            <input class="layui-input layui-disabled" type="text" value="{{$staff->last_login_ip}}" readonly >
        </div>
    </div>
@endif


<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go" >确 认</button>
        <a href="{{route('backend.platform.staff')}}" class="layui-btn layui-btn-sm" >返 回</a>
    </div>
</div>
