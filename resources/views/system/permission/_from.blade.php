{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">父级</label>
    <div class="layui-input-block">
        <select name="parent_id">
            <option value="0">顶级权限</option>
            @forelse($permissions as $p1)
                <option value="{{$p1->id}}" {{ isset($permission->id) && $p1->id == $permission->parent_id ? 'selected' : '' }} {{ isset($permission->id) && $p1->id == $permission->id ? 'disabled' : '' }} >{{$p1->display_name}}</option>
                @if($p1->childs->isNotEmpty())
                    @foreach($p1->childs as $p2)
                        <option value="{{$p2->id}}" {{ isset($permission->id) && $p2->id == $permission->parent_id ? 'selected' : '' }} {{ isset($permission->id) && $p2->id == $permission->id ? 'disabled' : '' }} >&nbsp;&nbsp;&nbsp;┗━━{{$p2->display_name}}</option>
                        @if($p2->childs->isNotEmpty())
                            @foreach($p2->childs as $p3)
                                <option value="{{$p3->id}}" {{ isset($permission->id) && $p3->id == $permission->parent_id ? 'selected' : '' }} {{ isset($permission->id) && $p3->id == $permission->id ? 'disabled' : '' }} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┗━━{{$p3->display_name}}</option>
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
    <label for="" class="layui-form-label">显示名称</label>
    <div class="layui-input-block">
        <input type="text" maxlength="16" name="display_name" value="{{$permission->display_name??old('display_name')}}" lay-verify="required" class="layui-input" placeholder="如：系统管理">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-block">
        <input type="text" maxlength="30" name="name" value="{{$permission->name??old('name')}}" lay-verify="required" class="layui-input" placeholder="如：system.index">
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go-close" >确 认</button>
    </div>
</div>

