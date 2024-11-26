@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('account.pay.check',['id'=>$model->id])}}" method="post">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">状态</label>
                    <div class="layui-input-block">
                        <input type="radio" name="status" value="1" title="通过" checked>
                        <input type="radio" name="status" value="2" title="不通过" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">备注</label>
                    <div class="layui-input-block">
                        <textarea name="check_result" class="layui-textarea" placeholder="审核不通过时备注"></textarea>
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
@endsection



