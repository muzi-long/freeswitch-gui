@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加分机</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.sip.store_list')}}" method="post" class="layui-form">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label class="layui-form-label">网关</label>
                    <div class="layui-input-inline">
                        <select name="gateway_id" lay-verify="required" >
                            <option value=""></option>
                            @foreach($gateways as $gw)
                                <option value="{{$gw->id}}" @if(isset($model)&&$model->gateway_id==$gw->id) selected @endif >{{$gw->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">开始分机</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="sip_start" lay-verify="required" value="{{old('sip_start')}}" placeholder="如：1000">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">结束分机</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="sip_end" lay-verify="required" value="{{old('sip_end')}}" placeholder="如：1010">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">密码</label>
                    <div class="layui-input-inline">
                        <input class="layui-input" type="text" name="password" lay-verify="required" value="{{old('password')}}" placeholder="如：1234">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn" lay-submit lay-filter="go" >确 认</button>
                        <a href="{{route('admin.sip')}}" class="layui-btn" >返 回</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('admin.sip._js')
@endsection