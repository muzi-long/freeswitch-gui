@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>更新</h2>
        </div>
        <div class="layui-card-body">
            <form action="{{route('crm.assignment.update',['id'=>$model->id])}}" method="post" class="layui-form">
                {{method_field('put')}}
                {{csrf_field()}}
                <div class="layui-row">
                    <div class="layui-col-md6">
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">客户名称</label>
                            <div class="layui-input-inline" style="width: 400px">
                                <input class="layui-input" type="text" name="name" lay-verify="required" value="{{$model->name}}" placeholder="请输入客户名称">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">联系人</label>
                            <div class="layui-input-inline" style="width: 400px">
                                <input class="layui-input" type="text" name="contact_name" lay-verify="required" value="{{$model->contact_name}}" placeholder="请输入联系人">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">联系电话</label>
                            <div class="layui-input-inline" style="width: 400px">
                                <input class="layui-input" type="number" name="contact_phone" lay-verify="required|phone" value="{{$model->contact_phone}}" placeholder="请输入联系电话">
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md6">
                        @foreach($fields as $d)
                            <div class="layui-form-item">
                                <label for="" class="layui-form-label">{{$d->field_label}}</label>
                                <div class="layui-input-inline" style="width: 400px">
                                    @switch($d->field_type)
                                        @case('input')
                                        <input type="input" class="layui-input" name="{{$d->field_key}}" value="{{$data[$d->id]??null}}" @if($d->required==1) lay-verify="required" @endif placeholder="{{$d->field_tips}}" >
                                        @break
                                        @case('textarea')
                                        <textarea name="{{$d->field_key}}" class="layui-textarea" @if($d->required==1) lay-verify="required" @endif placeholder="{{$d->field_tips}}">{{$data[$d->id]??null}}</textarea>
                                        @break
                                        @case('select')
                                        <select name="{{$d->field_key}}" @if($d->required==1) lay-verify="required" @endif>
                                            @if($d->field_option&&strpos($d->field_option,"\n"))
                                                @foreach(explode("\n",$d->field_option) as $v)
                                                    @php
                                                        $key = \Illuminate\Support\Str::before($v,':');
                                                        $val = \Illuminate\Support\Str::after($v,':');
                                                    @endphp
                                                    <option value="{{$key}}" @if(isset($data[$d->id])&&$key==$data[$d->id]) selected @endif >{{$val}}</option>
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
                                                <input type="radio" name="{{$d->field_key}}" value="{{$key}}" @if(isset($data[$d->id])&&$key==$data[$d->id]) checked @endif title="{{$val}}">
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
                                                    if (isset($data[$d->id])&&strpos($data[$d->id],',')){
                                                        $fieldValue = explode(",",$data[$d->id]);
                                                    }
                                                @endphp
                                                <input type="checkbox" name="{{$d->field_key}}[]" value="{{$key}}" @if(in_array($key,$fieldValue) || (isset($data[$d->id])&&$key==$data[$d->id]) ) checked @endif title="{{$val}}">
                                            @endforeach
                                        @endif
                                        @break
                                        @case('image')
                                        <div class="layui-upload">
                                            <button type="button" class="layui-btn layui-btn-sm uploadPic" data-ul="ul_{{$d->field_key}}" data-input="input_{{$d->field_key}}" ><i class="layui-icon">&#xe67c;</i>单图上传</button>
                                            <div class="layui-upload-list" >
                                                <ul class="layui-upload-box layui-clear" id="ul_{{$d->field_key}}">
                                                    @if(isset($data[$d->id]))
                                                        <li><img src="{{ $data[$d->id] }}" /><p onclick="removePic(this,'ul_{{$d->field_key}}','input_{{$d->field_key}}')">删除</p></li>
                                                    @endif
                                                </ul>
                                                <input type="hidden" class="layui-upload-input" id="input_{{$d->field_key}}" name="{{$d->field_key}}" value="{{$data[$d->id]??null}}">
                                            </div>
                                        </div>
                                        @break
                                        @case('images')
                                        <div class="layui-upload">
                                            <button type="button" class="layui-btn layui-btn-sm uploadPics" data-ul="ul_{{$d->field_key}}" data-input="input_{{$d->field_key}}" ><i class="layui-icon">&#xe67c;</i>多图上传</button>
                                            <div class="layui-upload-list" >
                                                <ul class="layui-upload-box layui-clear" id="ul_{{$d->field_key}}">
                                                    @if(isset($data[$d->id]) && strpos($data[$d->id],','))
                                                        @foreach(explode(',',$data[$d->id]) as $v)
                                                            <li><img src="{{ $v }}" /><p onclick="removePics(this,'ul_{{$d->field_key}}','input_{{$d->field_key}}')">删除</p></li>
                                                        @endforeach
                                                    @endif
                                                </ul>
                                                <input type="hidden" class="layui-upload-input" id="input_{{$d->field_key}}" name="{{$d->field_key}}" value="{{$data[$d->id]??null}}">
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
