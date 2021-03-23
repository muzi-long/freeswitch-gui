@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('crm.assignment.store')}}" method="post" class="layui-form">
                {{csrf_field()}}
                <div class="layui-row">
                    <div class="layui-col-md6">
                        <div class="layui-form-item">
                            <h2>基础信息</h2>
                        </div>
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">客户名称</label>
                            <div class="layui-input-inline" style="width: 400px">
                                <input class="layui-input" type="text" name="name"  placeholder="请输入客户名称">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">联系人</label>
                            <div class="layui-input-inline" style="width: 400px">
                                <input class="layui-input" type="text" name="contact_name"  placeholder="请输入联系人">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">联系电话</label>
                            <div class="layui-input-inline" style="width: 400px">
                                <input class="layui-input" type="number" name="contact_phone" lay-verify="required|phone"  placeholder="请输入联系电话">
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md6">
                        <div class="layui-form-item">
                            <h2>扩展信息</h2>
                        </div>
                        @foreach($fields as $d)
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
                                            <option value=""></option>
                                            @if($d->field_option&&strpos($d->field_option,"\n"))
                                                @foreach(explode("\n",$d->field_option) as $v)
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
                                        @if($d->field_option&&strpos($d->field_option,"\n"))
                                            @foreach(explode("\n",$d->field_option) as $v)
                                                @php
                                                    $key = \Illuminate\Support\Str::before($v,':');
                                                    $val = \Illuminate\Support\Str::after($v,':');
                                                @endphp
                                                <input type="radio" name="{{$d->field_key}}" value="{{$key}}" @if($key==$d->field_value) checked @endif title="{{$val}}">
                                            @endforeach
                                        @endif
                                        @break
                                        @case('checkbox')
                                        @if($d->field_option&&strpos($d->field_option,"\n"))
                                            @foreach(explode("\n",$d->field_option) as $v)
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
                                            <button type="button" class="layui-btn layui-btn-sm uploadPic"><i class="layui-icon">&#xe67c;</i>单图上传</button>
                                            <div class="layui-upload-list" >
                                                <ul class="layui-upload-box layui-clear">
                                                </ul>
                                                <input type="hidden" class="layui-upload-input" name="{{$d->field_key}}" value="{{$d->field_value}}">
                                            </div>
                                        </div>
                                        @break
                                        @case('images')
                                        <div class="layui-upload">
                                            <button type="button" class="layui-btn layui-btn-sm uploadPics" data-ul="ul_{{$d->field_key}}" data-input="input_{{$d->field_key}}" ><i class="layui-icon">&#xe67c;</i>多图上传</button>
                                            <div class="layui-upload-list" >
                                                <ul class="layui-upload-box layui-clear" id="ul_{{$d->field_key}}">
                                                </ul>
                                                <input type="hidden" class="layui-upload-input" id="input_{{$d->field_key}}" name="{{$d->field_key}}" value="{{$d->field_value}}">
                                            </div>
                                        </div>
                                        @break
                                        @default
                                        @break
                                    @endswitch
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go-close-refresh" >确认</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    @include('crm.assignment._js')
@endsection
