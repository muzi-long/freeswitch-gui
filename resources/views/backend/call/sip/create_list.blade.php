@extends('backend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加分机</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('backend.call.sip.storeList')}}" method="post" class="layui-form">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">商户</label>
                    <div class="layui-input-inline">
                        <select name="merchant_id" lay-verify="required" @if(isset($model)) disabled @endif lay-filter="merchant">
                            <option value="0"></option>
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
                    <label for="" class="layui-form-label">开始分机</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="sip_start" lay-verify="required" value="{{old('sip_start')}}" maxlength="4" placeholder="如：1000">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">结束分机</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="sip_end" lay-verify="required" value="{{old('sip_end')}}" maxlength="4" placeholder="如：1010">
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

            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('backend.call.sip._js')
@endsection
