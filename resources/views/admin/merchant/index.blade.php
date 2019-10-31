@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <form class="layui-form" >
                <div class="layui-btn-group">
                    @can('pbx.merchant.destroy')
                    <button class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删 除</button>
                    @endcan
                    @can('pbx.merchant.create')
                    <a class="layui-btn layui-btn-sm" href="{{ route('admin.merchant.create') }}">添 加</a>
                    @endcan
                    <button class="layui-btn layui-btn-sm" lay-submit lay-filter="search" >搜 索</button>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">公司名称</label>
                        <div class="layui-input-inline">
                            <input type="text" name="company_name" placeholder="公司名称" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">帐号</label>
                        <div class="layui-input-inline">
                            <input type="text" name="username" placeholder="商家帐号" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">状态</label>
                        <div class="layui-input-inline">
                            <select name="status">
                                <option value="">请选择</option>
                                @foreach(config('freeswitch.merchant_status') as $k => $v)
                                    <option value="{{$k}}">{{$v}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">到期时间</label>
                        <div class="layui-input-inline" style="width: 150px">
                            <input type="text" name="expires_at_start" id="expires_at_start" placeholder="开始时间" class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux">-</div>
                        <div class="layui-input-inline" style="width: 150px">
                            <input type="text" name="expires_at_end" id="expires_at_end" placeholder="结束时间" class="layui-input">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="options">
                <div class="layui-btn-group">
                    @can('pbx.bill')
                    <a class="layui-btn layui-btn-sm" lay-event="bill">帐单</a>
                    @endcan
                    @can('pbx.merchant.gateway')
                        <a class="layui-btn layui-btn-sm" lay-event="gateway">网关</a>
                    @endcan
                    @can('pbx.merchant.edit')
                    <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                    @endcan
                    @can('pbx.merchant.destroy')
                    <a class="layui-btn layui-btn-danger layui-btn-sm " lay-event="del">删除</a>
                    @endcan
                </div>
            </script>
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
            //用户表格初始化
            var dataTable = table.render({
                elem: '#dataTable'
                ,height: 500
                ,url: "{{ route('admin.merchant.data') }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'id', title: 'ID', sort: true,width:80}
                    ,{field: 'company_name', title: '公司名称'}
                    ,{field: 'username', title: '帐号'}
                    ,{field: 'status_name', title: '状态', templet:function (d) {
                        if (d.status==1){
                            return '<span class="layui-badge layui-bg-green">'+d.status_name+'</span>'
                        }else if (d.status==2){
                            return '<span class="layui-badge layui-bg-cyan">'+d.status_name+'</span>'
                        }else {
                            return ''
                        }
                    }}
                    ,{field: 'expires_at', title: '到期时间'}
                    ,{field: 'sip_num', title: '最大分机数'}
                    ,{field: 'sips_count', title: '已建分机数'}
                    ,{field: 'money', title: '帐户余额'}
                    ,{field: 'created_user_name', title: '创建人'}
                    ,{field: 'created_at', title: '创建时间'}
                    ,{fixed: 'right', width: 220, align:'center', toolbar: '#options', title:'操作'}
                ]]
            });

            //时间选择
            laydate.render({elem:'#expires_at_start',type:'datetime'})
            laydate.render({elem:'#expires_at_end',type:'datetime'})

            //监听工具条
            table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
                var data = obj.data //获得当前行数据
                    ,layEvent = obj.event; //获得 lay-event 对应的值
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        $.post("{{ route('admin.merchant.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                            if (result.code==0){
                                obj.del(); //删除对应行（tr）的DOM结构
                            }
                            layer.close(index);
                            var icon = result.code==0?6:5;
                            layer.msg(result.msg,{icon:icon})
                        });
                    });
                } else if(layEvent === 'edit'){
                    location.href = '/admin/merchant/'+data.id+'/edit';
                } else if (layEvent === 'bill'){
                    layer.open({
                        type : 2,
                        title : '商户帐单',
                        shadeClose : true,
                        area : ['80%','80%'],
                        content : '/admin/merchant/bill?merchant_id='+data.id
                    })
                } else if(layEvent === 'gateway'){
                    location.href = '/admin/merchant/'+data.id+'/gateway';
                }
            });

            //按钮批量删除
            $("#listDelete").click(function () {
                var ids = []
                var hasCheck = table.checkStatus('dataTable')
                var hasCheckData = hasCheck.data
                if (hasCheckData.length>0){
                    $.each(hasCheckData,function (index,element) {
                        ids.push(element.id)
                    })
                }
                if (ids.length>0){
                    layer.confirm('确认删除吗？', function(index){
                        $.post("{{ route('admin.merchant.destroy') }}",{_method:'delete',ids:ids},function (result) {
                            if (result.code==0){
                                dataTable.reload()
                            }
                            layer.close(index);
                            var icon = result.code==0?6:5;
                            layer.msg(result.msg,{icon:icon})
                        });
                    })
                }else {
                    layer.msg('请选择删除项',{icon:5})
                }
            })

            //搜索
            form.on('submit(search)', function(data){
                var parms = data.field;
                dataTable.reload({
                    where:parms,
                    page:{curr:1}
                });
                return false;
            });

        })
    </script>
@endsection