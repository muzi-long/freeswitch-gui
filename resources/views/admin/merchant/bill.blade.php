@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-row">
                <div class="layui-col-md8">
                    @can('pbx.bill')
                    <form class="layui-form">
                        <div class="layui-form-item">
                            <div class="layui-inline">
                                <label for="" class="layui-form-label">类型</label>
                                <div class="layui-input-inline" style="width: 100px;">
                                    <select name="type">
                                        <option value="">请选择</option>
                                        <option value="1">增加</option>
                                        <option value="2">减少</option>
                                    </select>
                                </div>

                                <label for="" class="layui-form-label">时间</label>
                                <div class="layui-input-inline" style="width: 150px">
                                    <input type="text" name="created_at_start" id="created_at_start" placeholder="开始时间" class="layui-input">
                                </div>
                                <div class="layui-form-mid layui-word-aux">-</div>
                                <div class="layui-input-inline" style="width: 150px">
                                    <input type="text" name="created_at_end" id="created_at_end" placeholder="结束时间" class="layui-input">
                                </div>
                                <div class="layui-input-inline" style="width: 100px">
                                    <button class="layui-btn" lay-submit lay-filter="search" >搜 索</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table id="dataTable" lay-filter="dataTable"></table>
                    @endcan
                </div>
                <div class="layui-col-md4">
                    @can('pbx.bill.create')
                    <form class="layui-form" >
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">公司名称</label>
                            <div class="layui-form-mid layui-word-aux">{{$merchant->company_name}}</div>
                        </div>
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">商家帐号</label>
                            <div class="layui-form-mid layui-word-aux">{{$merchant->username}}</div>
                        </div>
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">到期时间</label>
                            <div class="layui-form-mid layui-word-aux">{{$merchant->expires_at}}</div>
                        </div>
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">分机数量</label>
                            <div class="layui-form-mid layui-word-aux">{{$merchant->sip_num}}</div>
                        </div>
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">余额</label>
                            <div class="layui-form-mid layui-word-aux">{{$merchant->money}}</div>
                        </div>
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">金额</label>
                            <div class="layui-input-inline" style="width: 100px">
                                <select name="type" lay-verify="required">
                                    <option value="">请选择</option>
                                    <option value="1">增加</option>
                                    <option value="2">减少</option>
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <input type="number" lay-verify="required|number" name="money" placeholder="请输入金额" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label">备注</label>
                            <div class="layui-input-block">
                                <textarea name="remark" lay-verify="required" class="layui-textarea"></textarea>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label for="" class="layui-form-label"></label>
                            <div class="layui-input-inline">
                                <input type="hidden" name="merchant_id" value="{{$merchant->id}}">
                                <button type="submit" class="layui-btn" lay-submit lay-filter="create" >确 认</button>
                            </div>
                        </div>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer','table','form','element','laydate'],function () {
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            var laydate = layui.laydate;

            @can('pbx.bill')
            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 500
                ,url: "{{ route('admin.bill.data') }}" //数据接口
                ,where:{"merchant_id":"{{$merchant->id}}"}
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'type', title: '类型', width: 80, templet:function (d) {
                            return d.type==1?'<span class="layui-badge layui-bg-green">增加</span>':'<span class="layui-badge layui-bg-cyan">减少</span>';
                        }}
                    ,{field: 'money', title: '金额', width: 120}
                    ,{field: 'remark', title: '备注'}
                    ,{field: 'created_user_name', title: '创建人'}
                    ,{field: 'created_at', title: '时间'}
                ]]
            });
            //时间选择
            laydate.render({elem:'#created_at_start',type:'datetime'})
            laydate.render({elem:'#created_at_end',type:'datetime'})
            //搜索
            form.on('submit(search)', function (data) {
                parms = data.field;
                parms['merchant_id'] = "{{$merchant->id}}";
                dataTable.reload({
                    where:parms,
                    page:{curr:1}
                });
                return false;
            });
            @endcan

            @can('pbx.bill.create')
            //添加
            form.on('submit(create)', function(data){
                layer.load()
                var parms = data.field;
                $.ajax({
                    type:"post",
                    url:"{{route('admin.bill.store')}}",
                    data:parms,
                    success:function (res) {
                        layer.closeAll('loading');
                        layer.msg(res.msg,{},function () {
                            if (res.code==0){
                                location.reload()
                            }
                        })
                    },
                    error : function (msg) {
                        layer.closeAll('loading');
                        var json=JSON.parse(msg.responseText);
                        $.each(json.errors, function(idx, obj) {
                            layer.msg(obj[0],{icon:5});
                            return false;
                        });
                    }
                })
                return false;
            });
            @endcan
        })
    </script>
@endsection