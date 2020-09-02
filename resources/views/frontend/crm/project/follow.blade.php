@extends('frontend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('frontend.crm.project.follow',['id'=>$model->id])}}" method="post" class="layui-form">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">节点</label>
                    <div class="layui-input-inline">
                        <select name="new_node_id" lay-verify="required" >
                            <option value=""></option>
                            @foreach($nodes as $d)
                                <option value="{{$d->id}}" @if($model->node_id==$d->id) selected @endif >{{$d->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="layui-word-aux layui-form-mid"></div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">下次跟进</label>
                    <div class="layui-input-inline">
                        <input type="text" id="next_follow_at" name="next_follow_at" placeholder="请选择时间" lay-verify="required" readonly class="layui-input">
                    </div>
                    <div class="layui-word-aux layui-form-mid"></div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">备注</label>
                    <div class="layui-input-inline">
                        <textarea name="content" class="layui-textarea" lay-verify="required" ></textarea>
                    </div>
                    <div class="layui-word-aux layui-form-mid"></div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go_parent" >确 认</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('frontend.crm.project._js')
@endsection
