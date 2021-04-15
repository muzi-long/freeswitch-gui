{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">商户</label>
    <div class="layui-input-inline">
        <select name="merchant_id" lay-filter="merchant" lay-verify="required" @if(isset($model)&&$model->merchant_id) disabled @endif >
            <option value=""></option>
            @foreach($merchants as $d)
                <option value="{{$d->id}}" @if(isset($model)&&$d->id==$model->merchant_id) selected @endif >{{$d->company_name}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">上级部门</label>
    <div class="layui-input-inline">
        <select name="parent_id" id="department">
            <option value="0">无</option>
            @if(isset($departments))
                @foreach($departments as $p1)
                    <option value="{{$p1->id}}" {{ isset($model) && $p1->id == $model->parent_id ? 'selected' : '' }} {{ isset($model) && $p1->id == $model->id ? 'disabled' : '' }} >{{$p1->name}}</option>
                    @if($p1->childs->isNotEmpty())
                        @foreach($p1->childs as $p2)
                            <option value="{{$p2->id}}" {{ isset($model) && $p2->id == $model->parent_id ? 'selected' : '' }} {{ isset($model) && $p2->id == $model->id ? 'disabled' : '' }} >&nbsp;&nbsp;&nbsp;┗━━{{$p2->name}}</option>
                            @if($p2->childs->isNotEmpty())
                                @foreach($p2->childs as $p3)
                                    <option value="{{$p3->id}}" {{ isset($model) && $p3->id == $model->parent_id ? 'selected' : '' }} {{ isset($model) && $p3->id == $model->id ? 'disabled' : '' }} >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┗━━{{$p3->name}}</option>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endif
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">名称</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="name" lay-verify="required" value="{{$model->name??''}}" placeholder="请输入名称">
    </div>
    <div class="layui-form-mid layui-word-aux"></div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">排序</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="number" name="sort" lay-verify="required" value="{{$model->sort??10}}" >
    </div>
    <div class="layui-form-mid layui-word-aux"></div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go" >确 认</button>
        <a href="{{route('backend.crm.department')}}" class="layui-btn layui-btn-sm" >返 回</a>
    </div>
</div>
