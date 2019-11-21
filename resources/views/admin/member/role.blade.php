@extends('admin.base')

@section('content')
    <style>
        .layui-form-checkbox span{width: 100px}
    </style>
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>分配角色</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('admin.member.assignRole',['id'=>$member->id])}}" method="post">
                {{csrf_field()}}
                {{method_field('put')}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">帐号</label>
                    <div class="layui-word-aux layui-form-mid">{{ $member->username }}</div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">联系人</label>
                    <div class="layui-word-aux layui-form-mid">{{ $member->contact_name }}</div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">联系电话</label>
                    <div class="layui-word-aux layui-form-mid">{{ $member->contact_phone }}</div>
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
                        <button type="submit" class="layui-btn" lay-submit="" lay-filter="formDemo">确 认</button>
                        <a class="layui-btn" href="{{route('admin.member')}}" >返 回</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['element','form'],function () {

        })
    </script>
@endsection


