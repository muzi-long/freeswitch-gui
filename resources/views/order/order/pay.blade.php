@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-card">
                <form class="layui-form" action="{{route('order.order.pay',['id'=>$model->id])}}" method="post">
                    {{csrf_field()}}
                    <div class="layui-form-item">
                        <label for="" class="layui-form-label">付款方式</label>
                        <div class="layui-input-block">
                            <select name="pay_type" lay-verify="required" >
                                @foreach(config('freeswitch.pay_type') as $k => $v)
                                    <option value="{{$k}}">{{$v}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label for="" class="layui-form-label">付款金额</label>
                        <div class="layui-input-block">
                            <input type="number" name="money" placeholder="付款金额" lay-verify="required" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label for="" class="layui-form-label">备注内容</label>
                        <div class="layui-input-block">
                            <textarea name="content" class="layui-textarea" lay-verify="required"></textarea>
                        </div>
                        <div class="layui-word-aux layui-form-mid"></div>
                    </div>

                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button type="button" lay-submit lay-filter="go-close-refresh" class="layui-btn layui-btn-sm">确认</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection



