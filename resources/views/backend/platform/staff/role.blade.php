@extends('backend.base')

@section('content')
    <style>
        .layui-form-checkbox span{width: 100px}
    </style>
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>分配角色</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('backend.platform.staff.assignRole',['id'=>$user->id])}}" method="post">
                {{csrf_field()}}
                {{method_field('put')}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">昵称</label>
                    <div class="layui-word-aux layui-form-mid">{{ $user->nickname }}</div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">帐号</label>
                    <div class="layui-word-aux layui-form-mid">{{ $user->username }}</div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">角色</label>
                    <div class="layui-input-block" style="width: 700px">
                        @forelse($roles as $role)
                            <input type="checkbox" name="roles[]" value="{{$role->id}}" title="{{$role->display_name}}" {{ $role->own ? 'checked' : ''  }} >
                        @empty
                            <div class="layui-form-mid layui-word-aux">还没有角色</div>
                        @endforelse
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go">确 认</button>
                        <a class="layui-btn layui-btn-sm" href="{{route('backend.platform.staff')}}" >返 回</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('backend.platform.staff._js')
@endsection


