@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新用户</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('admin.user.update',['id'=>$user->id])}}" method="post">
                {{method_field('put')}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">昵称</label>
                    <div class="layui-input-inline">
                        <input type="text" maxlength="16" name="nickname" value="{{ $user->nickname ?? old('nickname') }}" lay-verify="required" placeholder="请输入昵称" class="layui-input" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">电话号码</label>
                    <div class="layui-input-inline">
                        <input type="text" name="phone" value="{{$user->phone??old('phone')}}" lay-verify="required|phone"  placeholder="请输入手机号" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">部门</label>
                    <div class="layui-input-inline">
                        <select name="department_id">
                            <option value="">无</option>
                            @forelse($departments as $p1)
                                <option value="{{$p1->id}}" {{ isset($user) && $p1->id == $user->department_id ? 'selected' : '' }} >{{$p1->name}}</option>
                                @if($p1->childs->isNotEmpty())
                                    @foreach($p1->childs as $p2)
                                        <option value="{{$p2->id}}" {{ isset($user) && $p2->id == $user->department_id ? 'selected' : '' }} >&nbsp;&nbsp;&nbsp;┗━━{{$p2->name}}</option>
                                        @if($p2->childs->isNotEmpty())
                                            @foreach($p2->childs as $p3)
                                                <option value="{{$p3->id}}" {{ isset($user) && $p3->id == $user->department_id ? 'selected' : '' }}  >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┗━━{{$p3->name}}</option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            @empty
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">分机</label>
                    <div class="layui-input-inline">
                        <select name="sip_id" lay-search>
                            <option value="">无</option>
                            @foreach($sips as $p1)
                                <option value="{{$p1->id}}" {{ isset($user) && $p1->id == $user->sip_id ? 'selected' : '' }} {{ isset($user) && in_array($p1->id,$user_sip) ? 'disabled' : '' }} >{{$p1->username}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-sm" lay-submit="" lay-filter="go">确 认</button>
                        <a  class="layui-btn layui-btn-sm" href="{{route('admin.user')}}" >返 回</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('admin.user._js')
@endsection

