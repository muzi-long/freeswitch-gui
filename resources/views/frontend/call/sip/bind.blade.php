@extends('frontend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('frontend.call.sip.bind')}}">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分机号</label>
                    <div class="layui-input-inline">
                        <input type="text" value="{{$sip->username}}" disabled class="layui-input layui-disabled">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">密码</label>
                    <div class="layui-input-inline">
                        <input type="text" value="{{$sip->password}}" disabled class="layui-input layui-disabled">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">员工</label>
                    <div class="layui-input-inline">
                        <select name="staff_id" lay-verify="required">
                            <option value=""></option>
                            @foreach($staffs as $d)
                                <option value="{{$d->id}}">{{$d->nickname}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label"></label>
                    <div class="layui-input-inline">
                        <input type="hidden" name="sip_id" value="{{$sip->id}}">
                        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go" >确 认</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer', 'table', 'form','element'], function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            var element = layui.element;

        });
    </script>
@endsection
