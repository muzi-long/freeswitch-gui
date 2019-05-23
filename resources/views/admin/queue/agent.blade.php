@extends('admin.base')

@section('content')
    <style>
        .layui-form-checkbox span{width: 800px;}
    </style>
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>队列【{{$queue->display_name}}】分配坐席</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('admin.queue.assignAgent',['id'=>$queue->id])}}" method="post">
                {{csrf_field()}}
                {{method_field('put')}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">选择坐席</label>
                    <div class="layui-input-block">
                        @forelse($agents as $agent)
                            <input type="checkbox" name="agents[]" value="{{$agent->name}}" title="名称：{{$agent->name}}，分机：{{str_after($agent->contact,'user/')}}， 坐席状态：{{$agent->status_name}}， 呼叫状态：{{$agent->state_name}}， 最大无应答次数：{{$agent->max_no_answer}}" {{ $queue->agents->isNotEmpty()&&$queue->agents->contains($agent) ? 'checked' : ''  }} >
                        @empty
                            <div class="layui-form-mid layui-word-aux">还没有坐席</div>
                        @endforelse
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="submit" class="layui-btn" lay-submit="" lay-filter="formDemo">确 认</button>
                        <a class="layui-btn" href="{{route('admin.queue')}}" >返 回</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection


