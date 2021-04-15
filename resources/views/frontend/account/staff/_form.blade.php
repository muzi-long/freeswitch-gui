{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">部门</label>
    <div class="layui-input-inline">
        <select name="department_id" id="department">
            <option value="0">无</option>
            @if(isset($departments))
                @foreach($departments as $p1)
                    <option value="{{$p1->id}}" {{ isset($staff) && $p1->id == $staff->department_id ? 'selected' : '' }}  >{{$p1->name}}</option>
                    @if($p1->childs->isNotEmpty())
                        @foreach($p1->childs as $p2)
                            <option value="{{$p2->id}}" {{ isset($staff) && $p2->id == $staff->department_id ? 'selected' : '' }}  >&nbsp;&nbsp;&nbsp;┗━━{{$p2->name}}</option>
                            @if($p2->childs->isNotEmpty())
                                @foreach($p2->childs as $p3)
                                    <option value="{{$p3->id}}" {{ isset($staff) && $p3->id == $staff->department_id ? 'selected' : '' }}  >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┗━━{{$p3->name}}</option>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endif
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

@if(!isset($staff))
<div class="layui-form-item">
    <label for="" class="layui-form-label">密码</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="password" name="password" lay-verify="required"  placeholder="请输入密码" >
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">确认密码</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="password" name="password_confirmation" lay-verify="required" placeholder="两次密码必需一致" >
    </div>
</div>
@endif

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
        <a href="{{route('frontend.account.staff')}}" class="layui-btn layui-btn-sm" >返 回</a>
    </div>
</div>
