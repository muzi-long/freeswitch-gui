@extends('frontend.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>客户详情</h2>
            @include('frontend.crm.project._btn')
        </div>
        <div class="layui-card-body">
            <div class="layui-row">
                <div class="layui-col-xs5">
                    <div class="layui-card">
                        <div class="layui-card-header"><b>客户信息</b></div>
                        <div class="layui-card-body">
                            <table class="layui-table" lay-skin="nob">
                                <tbody>
                                <tr>
                                    <td width="80" align="right">客户姓名：</td>
                                    <td>{{$model->contact_name}}</td>
                                    <td width="80" align="right">跟进时间：</td>
                                    <td>{{$model->follow_at}}</td>
                                </tr>
                                <tr>
                                    <td width="80" align="right">联系电话：</td>
                                    <td>{{$model->contact_phone}}</td>
                                    <td width="80" align="right">跟进人：</td>
                                    <td>{{$model->followUser->nickname}}</td>
                                </tr>
                                <tr>
                                    <td width="80" align="right">公司名称：</td>
                                    <td>{{$model->company_name}}</td>
                                    <td width="80" align="right">当前节点：</td>
                                    <td>{{$model->node->name}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="layui-col-xs5 layui-col-lg-offset2">
                    <div class="layui-card">
                        <div class="layui-card-header"><b>扩展信息</b></div>
                        <div class="layui-card-body">
                            <table class="layui-table" lay-skin="nob">
                                <tbody>
                                @foreach($model->designs as $d)
                                <tr>
                                    <td width="80" align="right">{{$d->field_label}}：</td>
                                    <td>
                                        @switch($d->field_type)
                                            @case('select')
                                                @if($d->field_option&&strpos($d->field_option,'|'))
                                                    @foreach(explode("|",$d->field_option) as $v)
                                                        @php
                                                            $key = \Illuminate\Support\Str::before($v,':');
                                                            $val = \Illuminate\Support\Str::after($v,':');
                                                        @endphp
                                                        @if($key==$d->pivot->data)
                                                            {{$val}}
                                                        @endif
                                                    @endforeach
                                                @endif
                                                @break
                                            @case('radio')
                                                @if($d->field_option&&strpos($d->field_option,'|'))
                                                    @foreach(explode("|",$d->field_option) as $v)
                                                        @php
                                                            $key = \Illuminate\Support\Str::before($v,':');
                                                            $val = \Illuminate\Support\Str::after($v,':');
                                                        @endphp
                                                        @if($key==$d->pivot->data)
                                                            {{$val}}
                                                        @endif
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
                                                        @if(in_array($key,$fieldValue) || $key==$d->pivot->data )
                                                            {{$val}}&nbsp;&nbsp;
                                                        @endif
                                                    @endforeach
                                                @endif
                                                @break
                                            @case('image')
                                                @if($d->pivot->data)
                                                    <a href="{{$d->pivot->data}}" target="_blank"><img src="{{$d->pivot->data}}" alt="" width="80" height="40"></a>
                                                @endif
                                                @break
                                            @default
                                                {{$d->pivot->data}}
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-row layui-col-space30">
                <div class="layui-card">
                    <div class="layui-card-header"><b>跟进记录</b></div>
                    <div class="layui-card-body">
                        <table id="dataTableFollow" lay-filter="dataTableFollow"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    @include('frontend.crm.project._js')
@endsection
