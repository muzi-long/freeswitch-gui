@extends('base')

@section('content')
    <style>
        .offline{
            font-size: 28px;
            font-weight: 600;
            color: #707070;
        }
        .online{
            font-size: 28px;
            font-weight: 600;
            color: #FF9B00;
        }
        .down{
            font-size: 28px;
            font-weight: 600;
            color: #80CD0e;
        }
        .active{
            font-size: 28px;
            font-weight: 600;
            color: #FF2A00;
        }
        .status-offline{
            width: 90px;
            height: 90px;
            margin-top: 4px;
            background: url("/layuiadmin/img/offline.png") no-repeat center center;
        }
        .status-online{
            width: 90px;
            height: 90px;
            margin-top: 4px;
            background: url("/layuiadmin/img/online.png") no-repeat center center;
        }
        .status-down{
            width: 90px;
            height: 90px;
            margin-top: 4px;
            background: url("/layuiadmin/img/down.png") no-repeat center center;
        }
        .status-active{
            width: 90px;
            height: 90px;
            margin-top: 4px;
            background: url("/layuiadmin/img/active.png") no-repeat center center;
        }
    </style>
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-row" id="count">
                <div class="layui-col-xs1">
                    <p><span class="offline">{{$count['offline']}}</span>离线</p>
                </div>
                <div class="layui-col-xs1">
                    <p><span class="online">{{$count['online']}}</span>在线</p>
                </div>
                <div class="layui-col-xs1">
                    <p><span class="down">{{$count['down']}}</span>空闲</p>
                </div>
                <div class="layui-col-xs1">
                    <p><span class="active">{{$count['active']}}</span>通话中</p>
                </div>
            </div>
        </div>
        <div class="layui-card-body">
            <div class="layui-row  layui-col-space10" style="min-height: 400px;">
                @foreach($data as $d)
                    <div class="layui-col-md2 extension-item" id="sipbox_{{$d->id}}">
                        <div style="border: 1px solid rgb(204, 204, 204)">
                            <div style="padding: 6px">
                                <div class="layui-row">
                                    <div class="layui-col-md5">
                                        <img class="status-offline">
                                    </div>
                                    <div class="layui-col-md7">
                                        <p>用户：{{$d->nickname}}</p>
                                        <p>分机：{{$d->username}}</p>
                                        <p>状态：<span class="status-txt">{{$d->status_name}}</span></p>
                                        <p>
                                            <button type="button" class="layui-btn layui-btn-sm" onclick="call({{$d->username}});">发起呼叫</button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.config({
            base: '/layuiadmin/modules/'
        }).extend({
            treetable: 'treetable-lay/treetable'
        }).use(['layer', 'table', 'form', 'treetable'], function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            function getSipStatus() {
                $.get("", function(res) {
                    //更新统计数据
                    $(".offline").text(res.data.count.offline);
                    $(".online").text(res.data.count.online);
                    $(".down").text(res.data.count.down);
                    $(".active").text(res.data.count.active);
                    //更新分机状态和状态图片
                    res.data.data.forEach(function (item,index) {
                        let _sip = $("#sipbox_"+item.id)
                        _sip.find("img").attr("class",item.class_name);
                        _sip.find(".status-txt").text(item.status_name)
                    })
                    setTimeout(getSipStatus,5000)
                })
            }
            getSipStatus();
        })
    </script>
@endsection
