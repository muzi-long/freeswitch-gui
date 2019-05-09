@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>拨号计划详情</h2>
        </div>
        <div class="layui-card-body">
            <table class="layui-table" lay-skin="nob">
                <tr>
                    @php
                    $tips1 = "名称：".$extension->display_name."<br/>标识符：".$extension->name."<br/>continue：".$extension->continue."<br/>类型：".$extension->context_name;
                    @endphp
                    <td onmouseleave="layer.closeAll()" onmouseenter="layer.tips('{{$tips1}}', this, {tips: 2,time:0});" >
                        <span>{{$extension->display_name}}</span>
                    </td>
                    <td>
                        @if($extension->conditions->isNotEmpty())
                        <table class="layui-table" lay-skin="row">
                            @foreach($extension->conditions as $condition)
                            <tr>
                                @php
                                $tips2 = "名称：".$condition->display_name."<br/>字段：".$condition->field."<br/>正则：".$condition->expression."<br/>break：".$condition->break;
                                @endphp
                                <td onmouseleave="layer.closeAll()" onmouseenter="layer.tips('{{$tips2}}', this, {tips: 4,time:0});" >
                                    <span class="layui-badge layui-bg-cyan">{{$condition->sort}}</span>
                                    <span>{{$condition->display_name}}</span>
                                </td>
                                <td>
                                    @if($condition->actions->isNotEmpty())
                                    <table class="layui-table" lay-skin="line">
                                        @foreach($condition->actions as $action)
                                        <tr>
                                            @php
                                                $tips3 = "名称：".$action->display_name."<br/>应用：".$action->application_name."<br/>数据：".$action->data;
                                            @endphp
                                            <td onmouseleave="layer.closeAll()" onmouseenter="layer.tips('{{$tips3}}', this, {tips: 4,time:0});">
                                                <span class="layui-badge layui-bg-cyan">{{$action->sort}}</span>
                                                <span>{{$action->display_name}}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </table>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </table>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
@endsection