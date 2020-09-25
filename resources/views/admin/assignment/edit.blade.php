@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('admin.assignment.update',['id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">公司名称</label>
                    <div class="layui-input-inline" style="width: 400px">
                        <input class="layui-input" type="text" name="company_name" lay-verify="required" value="{{$model->company_name??old('company_name')}}" placeholder="请输入公司名称">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">姓名</label>
                    <div class="layui-input-inline" style="width: 400px">
                        <input class="layui-input" type="text" name="name" lay-verify="required" value="{{$model->name??old('name')}}" placeholder="请输入名称">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">电话</label>
                    <div class="layui-input-inline" style="width: 400px">
                        <input class="layui-input" type="number" name="phone" lay-verify="required|phone" value="{{$model->phone??old('phone')}}" placeholder="请输入电话">
                    </div>
                </div>
                @foreach($model->designs as $d)
                    <div class="layui-form-item">
                        <label for="" class="layui-form-label">{{$d->field_label}}</label>
                        <div class="layui-input-inline" style="width: 400px">
                            @switch($d->field_type)
                                @case('input')
                                <input type="input" class="layui-input" name="{{$d->field_key}}" value="{{$d->pivot->data}}" @if($d->required==1) lay-verify="required" @endif placeholder="{{$d->field_tips}}" >
                                @break
                                @case('textarea')
                                <textarea name="{{$d->field_key}}" class="layui-textarea" @if($d->required==1) lay-verify="required" @endif placeholder="{{$d->field_tips}}">{{$d->pivot->data}}</textarea>
                                @break
                                @case('select')
                                <select name="{{$d->field_key}}" @if($d->required==1) lay-verify="required" @endif>
                                    @if($d->field_option&&strpos($d->field_option,'|'))
                                        @foreach(explode("|",$d->field_option) as $v)
                                            @php
                                                $key = \Illuminate\Support\Str::before($v,':');
                                                $val = \Illuminate\Support\Str::after($v,':');
                                            @endphp
                                            <option value="{{$key}}" @if($key==$d->pivot->data) selected @endif >{{$val}}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @break
                                @case('radio')
                                @if($d->field_option&&strpos($d->field_option,'|'))
                                    @foreach(explode("|",$d->field_option) as $v)
                                        @php
                                            $key = \Illuminate\Support\Str::before($v,':');
                                            $val = \Illuminate\Support\Str::after($v,':');
                                        @endphp
                                        <input type="radio" name="{{$d->field_key}}" value="{{$key}}" @if($key==$d->pivot->data) checked @endif title="{{$val}}">
                                    @endforeach
                                @endif
                                @break
                                @case('checkbox')
                                @if($d->field_option&&strpos($d->field_option,'|'))
                                    @foreach(explode("|",$d->field_option) as $v)
                                        @php
                                            $key = \Illuminate\Support\Str::before($v,':');
                                            $val = \Illuminate\Support\Str::after($v,':');
                                            $fieldValue = [];
                                            if ($d->pivot->data&&strpos($d->pivot->data,',')){
                                                $fieldValue = explode(",",$d->pivot->data);
                                            }
                                        @endphp
                                        <input type="checkbox" name="{{$d->field_key}}[]" value="{{$key}}" @if(in_array($key,$fieldValue) || $key==$d->pivot->data ) checked @endif title="{{$val}}">
                                    @endforeach
                                @endif
                                @break
                                @case('image')
                                <div class="layui-upload">
                                    <button type="button" class="layui-btn layui-btn-sm uploadPic"><i class="layui-icon">&#xe67c;</i>图片上传</button>
                                    <div class="layui-upload-list" >
                                        <ul class="layui-upload-box layui-clear">
                                            @if($d->pivot->data)
                                                <li><img src="{{ $d->pivot->data }}" /><p>上传成功</p></li>
                                            @endif
                                        </ul>
                                        <input type="hidden" class="layui-upload-input" name="{{$d->field_key}}" value="{{$d->pivot->data}}">
                                    </div>
                                </div>
                                @break
                                @default
                                @break
                            @endswitch
                        </div>
                    </div>
                @endforeach
                @include('admin.assignment._form')
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('admin.assignment._js')
@endsection
