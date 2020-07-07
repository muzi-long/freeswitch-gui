{{csrf_field()}}

<div class="layui-row layui-col-space10">
    <div class="layui-col-md3">
        <div class="layui-form-item">
            <label for="" class="layui-form-label">姓名</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="text" maxlength="16" name="nickname" lay-verify="required" value="{{$staff->nickname??old('nickname')}}" placeholder="如:张三">
            </div>
        </div>

        <div class="layui-form-item">
            <label for="" class="layui-form-label">帐号</label>
            <div class="layui-input-inline">
                @if(isset($staff))
                    <input class="layui-input layui-disabled" disabled type="text" value="{{$staff->username??old('username')}}" placeholder="如：zhangshang" >
                @else
                    <input class="layui-input" type="text" name="username" lay-verify="required" value="{{$staff->username??old('username')}}" placeholder="如：zhangshang" >
                @endif
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
        <div class="layui-form-item">
            <label for="" class="layui-form-label">到期时间</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="text" name="expire_at" value="{{$model->expire_at??''}}" id="expire_at" lay-verify="required" readonly placeholder="如：商户帐号有效期" >
            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label">服务器</label>
            <div class="layui-input-inline">
                <select name="freeswitch_id" >
                    <option value="0">无</option>
                    @foreach($fs as $d)
                        <option value="{{$d->id}}" {{isset($model)&&$model->freeswitch_id==$d->id?'selected':''}} >{{$d->name}}({{$d->external_ip}})</option>
                    @endforeach
                </select>
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
    </div>
    <div class="layui-col-md6">
        <div class="layui-form-item">
            <label for="" class="layui-form-label">公司名称</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="text" name="company_name" lay-verify="required" value="{{$model->company_name??old('company_name')}}" placeholder="如：中国李猜科技" >
            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label">联系人</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="text" name="contact_name" lay-verify="required" value="{{$model->contact_name??old('contact_name')}}" placeholder="如：李猜" >
            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label">联系电话</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="text" name="contact_phone" lay-verify="required" value="{{$model->contact_phone??old('contact_phone')}}" placeholder="如：18666666666" >
            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label">员工数量</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="number" name="staff_num" lay-verify="required" value="{{$model->staff_num??old('staff_num')}}" placeholder="商户可添加的员工数量" >
            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label">分机数量</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="number" name="sip_num" lay-verify="required" value="{{$model->sip_num??old('sip_num')}}" placeholder="商户可拥有的分机数量" >
            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label">网关数量</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="number" name="gateway_num" lay-verify="required" value="{{$model->gateway_num??old('gateway_num')}}" placeholder="商户可拥有的网关数量" >
            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label">坐席数量</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="number" name="agent_num" lay-verify="required" value="{{$model->agent_num??old('agent_num')}}" placeholder="商户可拥有的群呼坐席数量" >
            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label">队列数量</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="number" name="queue_num" lay-verify="required" value="{{$model->queue_num??old('queue_num')}}" placeholder="商户可拥有的群呼队列数量" >
            </div>
        </div>
        <div class="layui-form-item">
            <label for="" class="layui-form-label">任务数量</label>
            <div class="layui-input-inline">
                <input class="layui-input" type="number" name="task_num" lay-verify="required" value="{{$model->task_num??old('task_num')}}" placeholder="商户可拥有的群呼任务数量" >
            </div>
        </div>
    </div>
</div>

<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go" >确 认</button>
        <a href="{{route('backend.platform.merchant')}}" class="layui-btn layui-btn-sm" >返 回</a>
    </div>
</div>
