@extends('home.base')

@section('content')
    <link rel="stylesheet" href="/static/admin/layuiadmin/style/keyboard-call.css">
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>在线拨号</h2>
        </div>
        <div class="layui-card-body">
            <div class="body-main" style="min-height: 450px;">
                <div class="call-warp">
                    <div class="call-input">
                        <input type="text" id="telNum" autofocus="autofocus" style="box-sizing:border-box"/>
                        <a href="javascript:void(0)" class="del-num"></a>
                        <a href="javascript:void(0)" class="del-all"></a>
                    </div>
                    <div class="number-key" id="number">
                        <a href="javascript:void(0)">1</a>
                        <a href="javascript:void(0)">2</a>
                        <a href="javascript:void(0)">3</a>
                        <a href="javascript:void(0)">4</a>
                        <a href="javascript:void(0)">5</a>
                        <a href="javascript:void(0)">6</a>
                        <a href="javascript:void(0)">7</a>
                        <a href="javascript:void(0)">8</a>
                        <a href="javascript:void(0)">9</a>
                        <a href="javascript:void(0)">*</a>
                        <a href="javascript:void(0)">0</a>
                        <a href="javascript:void(0)">#</a>
                    </div>
                    <a href="javascript:void(0)" class="call-btn">
                        <i class="call-icon"></i>
                        <span class="inline">呼&nbsp;&nbsp;叫</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['form','element','table', 'layer','upload'],function() {
            var $ = layui.jquery;
            var form = layui.form;
            var element = layui.element;
            var table = layui.table;
            var layer = layui.layer;
            var upload = layui.upload;

            $('#number').children().click(function () {
                var num = $(this).text();
                var showNum = $('#telNum').val();
                $('#telNum').val(showNum + num);
            });
            $(document).keydown(function (e) {
                var keyNum = e.keyCode;
                var showNum = $('#telNum').val();
                var num = '';
                if (keyNum == 13) {
                    markCall();
                }
                if (e.target.id == 'telNum') {
                } else {
                    if (!e.shiftKey) {
                        switch (keyNum) {
                            case 96:
                                num = 0;
                                break;
                            case 48:
                                num = 0;
                                break;
                            case 97:
                                num = 1;
                                break;
                            case 49:
                                num = 1;
                                break;
                            case 98:
                                num = 2;
                                break;
                            case 50:
                                num = 2;
                                break;
                            case 99:
                                num = 3;
                                break;
                            case 51:
                                num = 3;
                                break;
                            case 100:
                                num = 4;
                                break;
                            case 52:
                                num = 4;
                                break;
                            case 101:
                                num = 5;
                                break;
                            case 53:
                                num = 5;
                                break;
                            case 102:
                                num = 6;
                                break;
                            case 54:
                                num = 6;
                                break;
                            case 103:
                                num = 7;
                                break;
                            case 55:
                                num = 7;
                                break;
                            case 104:
                                num = 8;
                                break;
                            case 56:
                                num = 8;
                                break;
                            case 105:
                                num = 9;
                                break;
                            case 57:
                                num = 9;
                                break;
                            case 106:
                                num = "*";
                                break;
                        }
                    } else {
                        switch (keyNum) {
                            case 51:
                                num = "#";
                                break;
                            case 56:
                                num = "*";
                                break;
                        }
                    }
                    switch (keyNum) {
                        //回删一个
                        case 8:
                            delStr();
                            num = '';
                            return false;
                        //回删所有
                        case 46:
                            $('#telNum').val('')
                            num = '';
                            return false;
                    }

                    $('#telNum').val(showNum + num);
                }

            });

            $('.del-num').click(function () {
                delStr();
            });
            $('.del-all').click(function () {
                $('#telNum').val('');
            });
            $('.call-btn').click(function () {
                markCall();
            });

            function markCall() {
                var ss = $("#telNum").val();
                $("#telNum").val(ss.replace(/\s/g, ""));
                call(ss);
            }
            function delStr() {
                var telStr = $('#telNum').val();
                telStr = telStr.substr(0, telStr.length - 1);
                $('#telNum').val(telStr);
            }
            function call(ss) {
                layer.msg('功能待开发')
            }
        });
    </script>
@endsection