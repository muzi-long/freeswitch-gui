@extends('admin.base')

@section('content')
    <style>
        .layui-form-checkbox span{width: 100px}
    </style>
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>分配网关</h2>
        </div>
        <div class="layui-card-body">
            <form class="layui-form" action="{{route('admin.merchant.assignGateway',['id'=>$merchant->id])}}" method="post">
                {{csrf_field()}}
                {{method_field('put')}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">帐号</label>
                    <div class="layui-form-mid layui-word-aux">{{$merchant->username}}</div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">公司名称</label>
                    <div class="layui-form-mid layui-word-aux">{{$merchant->company_name}}</div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">选择网关</label>
                    <div class="layui-input-block" style="width: 600px" >
                        <table class="layui-table" lay-size="sm">
                            <thead>
                            <tr>
                                <th width="20"><input type="checkbox" lay-ignore lay-skin="primary" id="checkAll" ></th>
                                <th>网关名称</th>
                                <th>网关帐号</th>
                                <th>注册地址</th>
                                <th width="100">费率（元/分钟）</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($gateways as $gateway)
                            <tr>
                                <td><input type="checkbox" class="checkItem" lay-ignore name="gateways[{{$loop->index}}][id]" value="{{$gateway->id}}" @if($merchant->gateways->contains($gateway)) checked @endif ></td>
                                <td>{{$gateway->name}}</td>
                                <td>{{$gateway->username}}</td>
                                <td>{{$gateway->realm}}</td>
                                <td><input type="text" name="gateways[{{$loop->index}}][rate]" value="{{$gateway->rate}}" class="layui-input" lay-verify="required|number" style="height:26px;line-height: 26px"></td>
                            </tr>
                            @empty
                            <tr><td colspan="5">没有可分配的网关，请联系管理员</td></tr>
                            </tbody>
                            @endforelse
                        </table>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="submit" class="layui-btn" lay-submit="" lay-filter="formDemo">确 认</button>
                        <a class="layui-btn" href="{{route('admin.merchant')}}" >返 回</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer','table','form'],function () {
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            
            $("#checkAll").click(function () {
                var pop = $(this).prop('checked');
                $(".checkItem").prop('checked',pop);
            })
            
        })
    </script>
@endsection


