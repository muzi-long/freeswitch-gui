@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-row layui-col-space30">
                <div class="layui-col-xs6">
                    <div class="layui-card">
                        <div class="layui-card-header"><b>付款记录</b></div>
                        <div class="layui-card-body">
                            <ul class="layui-timeline" id="pay_list_box"></ul>
                        </div>
                    </div>
                </div>
                <div class="layui-col-xs6">
                    <div class="layui-card">
                        <div class="layui-card-header"><b>新增付款</b></div>
                        <div class="layui-card-body">
                            <form class="layui-form" action="{{route('order.order.pay',['id'=>$model->id])}}" method="post">
                            {{csrf_field()}}
                            <div class="layui-form-item">
                                <label for="" class="layui-form-label">付款方式</label>
                                <div class="layui-input-block">
                                    <select name="pay_type" lay-verify="required" >
                                        @foreach(config('freeswitch.pay_type') as $k => $v)
                                            <option value="{{$k}}">{{$v}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="" class="layui-form-label">付款金额</label>
                                <div class="layui-input-block">
                                    <input type="number" name="money" placeholder="付款金额" lay-verify="required" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="" class="layui-form-label">备注内容</label>
                                <div class="layui-input-block">
                                    <textarea name="content" class="layui-textarea" lay-verify="required"></textarea>
                                </div>
                                <div class="layui-word-aux layui-form-mid"></div>
                            </div>

                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <button type="button" lay-submit lay-filter="go-close-refresh" class="layui-btn layui-btn-sm">确认</button>
                                </div>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer','table','form','element','flow','laydate'],function () {
            var $ = layui.jquery;
            var form = layui.form;
            var flow = layui.flow;
            var laydate = layui.laydate;
            flow.load({
                elem: '#pay_list_box' //流加载容器
                ,done: function(page, next){ //执行下一页的回调
                    $.post('{{route('api.payList')}}',{id:'{{$model->id}}',page:page},function (res) {
                        var _html = '';
                        res.data.list.forEach(function (item,index) {
                            _html += '<li class="layui-timeline-item">';
                            _html += '  <i class="layui-icon layui-timeline-axis">&#xe63f;</i>';
                            _html += '  <div class="layui-timeline-content layui-text">';
                            _html += '      <h3 class="layui-timeline-title">【'+item.status_name+'】'+item.created_at+'</h3>';
                            _html += '      <p><b>付款金额：</b>' + item.money + '</p>';
                            _html += '      <p><b>付款方式：</b>' + item.pay_type_name + '</p>';
                            _html += '      <p><b>备注：</b>' + item.content + '</p>';
                            if(item.status==2){
                                _html += '      <p><b>审核人：</b>' + item.check_user_nickname + '</p>';
                                _html += '      <p><b>审核时间：</b>' + item.check_time + '</p>';
                                _html += '      <p><b>原因：</b>' + item.check_result + '</p>';
                            }
                            _html += '  </div>';
                            _html += '</li>';
                        })
                        next(_html, page < res.data.lastPage); //假设总页数为 10
                    });

                }
            });
        });
    </script>
@endsection

