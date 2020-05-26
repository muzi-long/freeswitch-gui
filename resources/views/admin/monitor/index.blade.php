@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <div class="layui-row" id="count">
                <div class="layui-col-xs1">
                    <p><span id="total" style="font-size: 28px;font-weight: 600;color: rgb(57, 107, 255)">0</span>分机</p>
                </div>
                <div class="layui-col-xs1">
                    <p><span id="active" style="font-size: 28px;font-weight: 600;color: rgb(255, 40, 3)">0</span>通话中</p>
                </div>
                <div class="layui-col-xs1">
                    <p><span id="down" style="font-size: 28px;font-weight: 600;color: rgb(147, 207, 47)">0</span>空闲</p>
                </div>
                <div class="layui-col-xs1">
                    <p><span id="ring" style="font-size: 28px;font-weight: 600;color: rgb(255, 168, 1)">0</span>响铃中</p>
                </div>
            </div>
        </div>
        <div class="layui-card-body">
            <div class="layui-row  layui-col-space10" style="min-height: 400px;">
                @foreach($data as $d)
                <div class="layui-col-md2 extension-item" extension-id="{{$d->id}}">
                    <div style="border: 1px solid rgb(204, 204, 204)">
                        <div style="padding: 6px">
                            <div class="layui-row">
                                <div class="layui-col-md4">
                                    <img class="status-img" style="margin-top: 4px" src="/layuiadmin/img/status_{{$d->state}}.png" alt="">
                                </div>
                                <div class="layui-col-md8">
                                    <p>分机：{{$d->username}}</p>
                                    <p>状态：<span class="status-txt">{{$d->state}}</span></p>
                                    <p>
                                        监听：
                                        <a onclick="chanspy({{$d->username}},1);" title="客户听不到监听者说话(常用)" style="cursor: pointer">密语</a>
                                        <a onclick="chanspy({{$d->username}},2);" title="只能听" style="cursor: pointer">旁听</a>
                                        <a onclick="chanspy({{$d->username}},3);" title="三方正常通话" style="cursor: pointer">强插</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="layui-row">
                <style>
                    .pagination{display:inline-block;padding-left:0;margin:20px 0;border-radius:4px}
                    .pagination>li{display:inline}
                    .pagination>li>a,.pagination>li>span{position:relative;float:left;padding:6px 12px;margin-left:-1px;line-height:1.42857143;color:#337ab7;text-decoration:none;background-color:#fff;border:1px solid #ddd}
                    .pagination>li:first-child>a,.pagination>li:first-child>span{margin-left:0;border-top-left-radius:4px;border-bottom-left-radius:4px}
                    .pagination>li:last-child>a,.pagination>li:last-child>span{border-top-right-radius:4px;border-bottom-right-radius:4px}
                    .pagination>li>a:focus,.pagination>li>a:hover,.pagination>li>span:focus,.pagination>li>span:hover{z-index:2;color:#23527c;background-color:#eee;border-color:#ddd}
                    .pagination>.active>a,.pagination>.active>a:focus,.pagination>.active>a:hover,.pagination>.active>span,.pagination>.active>span:focus,.pagination>.active>span:hover{z-index:3;color:#fff;cursor:default;background-color:#337ab7;border-color:#337ab7}
                    .pagination>.disabled>a,.pagination>.disabled>a:focus,.pagination>.disabled>a:hover,.pagination>.disabled>span,.pagination>.disabled>span:focus,.pagination>.disabled>span:hover{color:#777;cursor:not-allowed;background-color:#fff;border-color:#ddd}
                </style>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form','element','table', 'layer', 'laydate'],function() {
            var $ = layui.jquery;
            function getExtenStatus() {
                $.get("{{route('admin.monitor')}}", function(res) {
                    //更新统计数据
                    $("#count").find("#total").text(res.data.count.total);
                    $("#count").find("#down").text(res.data.count.down);
                    $("#count").find("#ring").text(res.data.count.ring);
                    $("#count").find("#active").text(res.data.count.active);
                    //更新分机状态和状态图片
                    $(".extension-item").each(function(index,elem) {
                        var exten = $(elem).attr("extension-id");
                        $(elem).find(".status-img").attr("src","/layuiadmin/img/status_"+res['data']['list'][exten]['state']+".png");
                        $(elem).find(".status-txt").text(res['data']['list'][exten]['state_name'])
                    });
                    setTimeout(getExtenStatus,5000)
                })
            }
            getExtenStatus();
        });
    </script>
@endsection