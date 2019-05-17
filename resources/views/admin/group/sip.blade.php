@extends('admin.base')

@section('content')
    <style>
        .layui-form-checkbox span{width: 100px}
    </style>
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>组【{{$group->display_name}}】分配分机</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('admin.group.assignSip',['id'=>$group])}}" method="post">
                {{csrf_field()}}
                {{method_field('put')}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">选择分机</label>
                    <div class="layui-input-block">
                        @forelse($sips as $sip)
                            <input type="checkbox" name="sips[]" value="{{$sip->id}}" title="{{$sip->username}}-{{$sip->effective_caller_id_name}}" {{ $group->sips->isNotEmpty()&&$group->sips->contains($sip) ? 'checked' : ''  }} >
                        @empty
                            <div class="layui-form-mid layui-word-aux">还没有分机</div>
                        @endforelse
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="submit" class="layui-btn" lay-submit="" lay-filter="formDemo">确 认</button>
                        <a class="layui-btn" href="{{route('admin.group')}}" >返 回</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection


