@extends('base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <form action="{{route('order.order.store',['customer_id'=>$model->id])}}" method="post" class="layui-form">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">客户名称</label>
                    <div class="layui-input-block">
                        <input class="layui-input layui-disabled" type="text"  value="{{$model->name}}" readonly>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">联系人</label>
                    <div class="layui-input-block">
                        <input class="layui-input layui-disabled" type="text"  value="{{$model->contact_name}}" readonly>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">联系电话</label>
                    <div class="layui-input-block">
                        <input class="layui-input layui-disabled" type="text"  value="{{$model->contact_phone}}" readonly>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">接单人</label>
                    <div class="layui-input-block">
                        @include('common.get_user')
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">总金额</label>
                    <div class="layui-input-block">
                        <input class="layui-input" id="total_money" type="number" name="total_money" placeholder="请输入总金额">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">前期款</label>
                    <div class="layui-inline">
                        <div class="layui-input-inline" style="width: 80px">
                            <select name="first_percent" lay-filter="money_percent" data-name="first_money">
                                @for($i=0;$i<=20;$i++)
                                    <option value="{{round($i*5/100,2)}}">{{$i*5}}%</option>
                                @endfor
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <input class="layui-input" type="number" value="0" name="first_money" >
                        </div>
                        <div class="layui-word-aux layui-form-mid">根据总金额和比例自动结算</div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">中期款</label>
                    <div class="layui-inline">
                        <div class="layui-input-inline" style="width: 80px">
                            <select name="mid_percent" lay-filter="money_percent" data-name="mid_money">
                                @for($i=0;$i<=20;$i++)
                                    <option value="{{round($i*5/100,2)}}">{{$i*5}}%</option>
                                @endfor
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <input class="layui-input" type="number" value="0" name="mid_money" >
                        </div>
                        <div class="layui-word-aux layui-form-mid">根据总金额和比例自动结算</div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">尾款</label>
                    <div class="layui-inline">
                        <div class="layui-input-inline" style="width: 80px">
                            <select name="last_percent"lay-filter="money_percent" data-name="last_money">
                                @for($i=0;$i<=20;$i++)
                                    <option value="{{round($i*5/100,2)}}">{{$i*5}}%</option>
                                @endfor
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <input class="layui-input" type="number" value="0" name="last_money" >
                        </div>
                        <div class="layui-word-aux layui-form-mid">根据总金额和比例自动结算</div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn layui-btn-sm" lay-submit lay-filter="go-close-refresh" >确认</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['element', 'form', 'layer', 'table', 'upload', 'laydate', 'jquery'], function () {
            var $ = layui.jquery;
            var form = layui.form;
            var table = layui.table;
            var layer = layui.layer;

            form.on('select(money_percent)', function(data){
                if($("#total_money").val() == '' || $("#total_money").val() <= 0){
                    $("#total_money").focus()
                    layer.msg('请输入总金额',{icon:2})
                    return false
                }
                var name = $(data.elem).data('name')
                $('input[name="'+name+'"]').val((data.value * $("#total_money").val()).toFixed(2))
            });

        });
    </script>
@endsection
