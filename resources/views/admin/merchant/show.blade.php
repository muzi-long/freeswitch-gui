@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header layuiadmin-card-header-auto">
            <h2>商户详情</h2>
            <div class="layui-form">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">帐号</label>
                        <div class="layui-form-mid layui-word-aux" style="width: 140px">
                            {{$merchant->username}}
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">公司名称</label>
                        <div class="layui-form-mid layui-word-aux" style="width: 140px">
                            {{$merchant->info->company_name}}
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">分机数</label>
                        <div class="layui-form-mid layui-word-aux" style="width: 140px">
                            共{{$merchant->info->sip_num}}个
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">员工数</label>
                        <div class="layui-form-mid layui-word-aux" style="width: 140px">
                            共{{$merchant->info->member_num}}个
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">状态</label>
                        <div class="layui-form-mid layui-word-aux" style="width: 140px">
                            {{$merchant->status_name}}
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label for="" class="layui-form-label">到期时间</label>
                        <div class="layui-form-mid layui-word-aux" style="width: 140px">
                            {{$merchant->info->expires_at}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-card-body">
            <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
                <ul class="layui-tab-title">
                    <li class="layui-this">网关</li>
                    <li >角色</li>
                    <li >员工</li>
                    <li >分机</li>
                </ul>
                <div class="layui-tab-content">
                    <div class="layui-tab-item layui-show">
                        <form class="layui-form" action="{{route('admin.merchant.assignGateway',['id'=>$merchant->id])}}" method="post">
                            @can('portal.merchant.gateway')
                            {{csrf_field()}}
                            {{method_field('put')}}
                            <div class="layui-form-item">
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
                            <div class="layui-form-item">
                                <button type="submit" class="layui-btn" lay-submit="" lay-filter="formDemo">确 认</button>
                                <a class="layui-btn" href="{{route('admin.merchant')}}" >返 回</a>
                            </div>
                            @else
                            无权限查看
                            @endcan
                        </form>
                    </div>
                    <div class="layui-tab-item">
                        <form class="layui-form" method="post">
                            {{csrf_field()}}
                            {{method_field('put')}}
                            <div class="layui-form-item">
                                @forelse($roles as $role)
                                    <input type="checkbox" name="roles[]" value="{{$role->id}}" title="{{$role->display_name}}" {{ $role->own ? 'checked' : ''  }} >
                                @empty
                                    <div class="layui-form-mid layui-word-aux">还没有角色</div>
                                @endforelse
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <button type="submit" class="layui-btn" lay-submit="" lay-filter="assignRole">确 认</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="layui-tab-item">
                        <table class="layui-table" lay-size="sm">
                            <thead>
                            <tr>
                                <th>帐号</th>
                                <th>联系人</th>
                                <th>联系电话</th>
                                <th>分机号</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($accounts as $val)
                                <tr>
                                    <td>{{$val->username}}</td>
                                    <td>{{$val->contact_name}}</td>
                                    <td>{{$val->contact_phone}}</td>
                                    <td>{{$val->sip->username}}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4">无数据</td></tr>
                            </tbody>
                            @endforelse
                        </table>
                    </div>
                    <div class="layui-tab-item">
                        <table class="layui-table" lay-size="sm">
                            <thead>
                            <tr>
                                <th>帐号</th>
                                <th>密码</th>
                                <th>外显名称</th>
                                <th>外显号码</th>
                                <th>出局名称</th>
                                <th>出局号码</th>
                                <th>创建时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($sips as $val)
                                <tr>
                                    <td>{{$val->username}}</td>
                                    <td>{{$val->password}}</td>
                                    <td>{{$val->effective_caller_id_name}}</td>
                                    <td>{{$val->effective_caller_id_number}}</td>
                                    <td>{{$val->outbound_caller_id_name}}</td>
                                    <td>{{$val->outbound_caller_id_number}}</td>
                                    <td>{{$val->created_at}}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7">无数据</td></tr>
                            </tbody>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['layer','table','form','element'],function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            var element = layui.element;

            $("#checkAll").click(function () {
                var pop = $(this).prop('checked');
                $(".checkItem").prop('checked',pop);
            });

            form.on('submit(assignRole)',function (data) {
                var load = layer.load();
                $.post('{{route('admin.merchant.assignRole',['id'=>$merchant->id])}}',data.field,function (res) {
                    layer.close(load);
                    if (res.code == 0) {
                        layer.msg(res.msg, {icon: 1})
                    } else {
                        layer.msg(res.msg, {icon: 2})
                    }
                });
                return false;
            })

        })
    </script>
@endsection
