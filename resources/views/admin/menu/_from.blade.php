{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">权限</label>
    <div class="layui-input-inline">
        <select name="permission_id">
            <option value="0">顶级权限</option>
            @foreach($permissions as $p1)
                <option value="{{$p1->id}}" {{ isset($menu->permission_id) && $p1->id == $menu->permission_id ? 'selected' : '' }}  >{{$p1->display_name}}</option>
                @if($p1->childs->isNotEmpty())
                    @foreach($p1->childs as $p2)
                        <option value="{{$p2->id}}" {{ isset($menu->permission_id) && $p2->id == $menu->permission_id ? 'selected' : '' }}  >&nbsp;&nbsp;&nbsp;┗━━{{$p2->display_name}}</option>
                        @if($p2->childs->isNotEmpty())
                            @foreach($p2->childs as $p3)
                                <option value="{{$p3->id}}" {{ isset($menu->permission_id) && $p3->id == $menu->permission_id ? 'selected' : '' }}  >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┗━━{{$p3->display_name}}</option>
                            @endforeach
                        @endif
                    @endforeach
                @endif
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">父级</label>
    <div class="layui-input-inline">
        <select name="parent_id">
            <option value="0">顶级菜单</option>
            @foreach($menus as $p1)
                <option value="{{$p1->id}}" {{ isset($menu->id) && $p1->id == $menu->parent_id ? 'selected' : '' }} {{ isset($menu->id) && $p1->id == $menu->id ? 'disabled' : '' }} >{{$p1->name}}</option>
                @if($p1->childs->isNotEmpty())
                    @foreach($p1->childs as $p2)
                        <option value="{{$p2->id}}" {{ isset($menu->id) && $p2->id == $menu->parent_id ? 'selected' : '' }} {{ isset($menu->id) && $p2->id == $menu->id ? 'disabled' : '' }} >&nbsp;&nbsp;&nbsp;┗━━{{$p2->name}}</option>
                        @if($p2->childs->isNotEmpty())
                            @foreach($p2->childs as $p3)
                                <option value="{{$p3->id}}" {{ isset($menu->id) && $p3->id == $menu->parent_id ? 'selected' : '' }} {{ isset($menu->id) && $p3->id == $menu->id ? 'disabled' : '' }} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┗━━{{$p3->name}}</option>
                            @endforeach
                        @endif
                    @endforeach
                @endif
            @endforeach
        </select>
    </div>
</div>

<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-inline">
        <input type="text" name="name" value="{{$menu->name??old('name')}}" lay-verify="required" class="layui-input" placeholder="如：系统管理">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">链接</label>
    <div class="layui-input-inline">
        <input type="text" name="url" value="{{$menu->url??old('url')}}"  class="layui-input" placeholder="如：/admin/user">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">路由</label>
    <div class="layui-input-inline">
        <input type="text" name="route" value="{{$menu->route??old('route')}}" class="layui-input" placeholder="如：admin.user">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">排序</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="number" name="sort" value="{{$menu->sort??10}}" placeholder="" >
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">类型</label>
    <div class="layui-input-inline">
        <input type="radio" name="type" value="1" title="菜单" @if(!isset($menu)||(isset($menu)&&$menu->type==1)) checked @endif>
        <input type="radio" name="type" value="2" title="按钮" @if(isset($menu)&&$menu->type==2) checked @endif >
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">图标</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="hidden" name="icon" value="{{$menu->icon??''}}" >
    </div>
    <div class="layui-form-mid layui-word-aux" id="icon_box">
        <i class="layui-icon {{$menu->icon??''}}"></i>
    </div>
    <div class="layui-form-mid layui-word-aux">
        <button type="button" class="layui-btn layui-btn-xs" onclick="showIconsBox()">选择图标</button>
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go" >确 认</button>
        <a href="{{route('admin.menu')}}" class="layui-btn layui-btn-sm"  >返 回</a>
    </div>
</div>

