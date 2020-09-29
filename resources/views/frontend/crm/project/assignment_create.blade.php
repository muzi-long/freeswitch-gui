@extends('frontend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>添加项目</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('frontend.crm.assignment.store')}}" method="post" class="layui-form">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">公司名称</label>
                    <div class="layui-input-inline" style="width: 400px">
                        <input class="layui-input" type="text" name="company_name" lay-verify="required" value="{{$model->company_name??old('company_name')}}" placeholder="请输入公司名称">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">联系人</label>
                    <div class="layui-input-inline" style="width: 400px">
                        <input class="layui-input" type="text" name="contact_name" lay-verify="required" value="{{$model->contact_name??old('contact_name')}}" placeholder="请输入名称">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">联系电话</label>
                    <div class="layui-input-inline" style="width: 400px">
                        <input class="layui-input" type="number" name="contact_phone" lay-verify="required|phone" value="{{$model->contact_phone??old('contact_phone')}}" placeholder="请输入联系电话">
                    </div>
                </div>
                @foreach($designs as $d)
                    <div class="layui-form-item">
                        <label for="" class="layui-form-label">{{$d->field_label}}</label>
                        <div class="layui-input-inline" style="width: 400px">
                            @switch($d->field_type)
                                @case('input')
                                <input type="input" class="layui-input" name="{{$d->field_key}}" value="{{$d->field_value}}" @if($d->required==1) lay-verify="required" @endif placeholder="{{$d->field_tips}}" >
                                @break
                                @case('textarea')
                                <textarea name="{{$d->field_key}}" class="layui-textarea" @if($d->required==1) lay-verify="required" @endif placeholder="{{$d->field_tips}}">{{$d->field_value}}</textarea>
                                @break
                                @case('select')
                                <select name="{{$d->field_key}}" @if($d->required==1) lay-verify="required" @endif>
                                    @if($d->field_option&&strpos($d->field_option,'|'))
                                        @foreach(explode("|",$d->field_option) as $v)
                                            @php
                                                $key = \Illuminate\Support\Str::before($v,':');
                                                $val = \Illuminate\Support\Str::after($v,':');
                                            @endphp
                                            <option value="{{$key}}" @if($key==$d->field_value) selected @endif >{{$val}}</option>
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
                                        <input type="radio" name="{{$d->field_key}}" value="{{$key}}" @if($key==$d->field_value) checked @endif title="{{$val}}">
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
                                            if ($d->field_value&&strpos($d->field_value,',')){
                                                $fieldValue = explode(",",$d->field_value);
                                            }
                                        @endphp
                                        <input type="checkbox" name="{{$d->field_key}}[]" value="{{$key}}" @if(in_array($key,$fieldValue) || $key==$d->field_value ) checked @endif title="{{$val}}">
                                    @endforeach
                                @endif
                                @break
                                @case('image')
                                <div class="layui-upload">
                                    <button type="button" class="layui-btn layui-btn-sm uploadPic"><i class="layui-icon">&#xe67c;</i>图片上传</button>
                                    <div class="layui-upload-list" >
                                        <ul class="layui-upload-box layui-clear">
                                        </ul>
                                        <input type="hidden" class="layui-upload-input" name="{{$d->field_key}}" value="{{$d->field_value}}">
                                    </div>
                                </div>
                                @break
                                @default
                                @break
                            @endswitch
                        </div>
                    </div>
                @endforeach
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go" >确 认</button>
                        <a href="{{route('frontend.crm.assignment')}}" class="layui-btn layui-btn-sm" >返 回</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('frontend.crm.project._js')
@endsection
