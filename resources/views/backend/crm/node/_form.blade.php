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
    <label for="" class="layui-form-label">类型</label>
    <div class="layui-input-inline">
        <select name="type" lay-verify="required"  >
            <option value=""></option>
            @foreach(config('freeswitch.node_type') as $k => $v)
            <option value="{{$k}}" @if(isset($model)&&$model->type==$k) selected @endif >{{$v}}</option>
            @endforeach
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
        <input class="layui-input" type="number" name="sort" lay-verify="required|number" value="{{$model->sort??10}}" placeholder="">
    </div>
    <div class="layui-form-mid layui-word-aux"></div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go" >确 认</button>
        <a href="{{route('backend.crm.node')}}" class="layui-btn layui-btn-sm" >返 回</a>
    </div>
</div>
