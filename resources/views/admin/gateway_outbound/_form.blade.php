{{csrf_field()}}
<div class="layui-form-item">
    <label for="" class="layui-form-label">网关</label>
    <div class="layui-input-inline">
        <select name="gateway_id" lay-verify="required" >
            <option value=""></option>
            @foreach($gateways as $d1)
                <option value="{{$d1->id}}" @if(isset($model)&&$model->gateway_id==$d1->id) selected @endif>{{$d1->name}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">号码</label>
    <div class="layui-input-inline">
        <input class="layui-input" type="text" name="number" lay-verify="required" value="{{$model->number??old('number')}}" placeholder="请输入号码">
    </div>
</div>
<div class="layui-form-item">
    <label for="" class="layui-form-label">状态</label>
    <div class="layui-input-inline">
        <input type="radio" name="status" value="1" title="启用" @if(!isset($model)||(isset($model)&&$model->status==1)) checked @endif >
        <input type="radio" name="status" value="2" title="禁用" @if(isset($model)&&$model->status==2) checked @endif >
    </div>
</div>
<div class="layui-form-item">
    <div class="layui-input-block">
        <button type="button" class="layui-btn" lay-submit lay-filter="*" >确 认</button>
    </div>
</div>