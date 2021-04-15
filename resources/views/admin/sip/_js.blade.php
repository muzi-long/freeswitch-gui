<script>
    layui.config({
        base: '/static/admin/layuiadmin/modules/'
    }).extend({
        selectN: 'select-ext/selectN'
    }).use(['layer','table','form','selectN'],function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;
        var table = layui.table;
        var selectN = layui.selectN;

        var mg_selected = [];
        @if(isset($model))
            mg_selected = ['{{$model->merchant_id}}','{{$model->gateway_id}}'];
        @endif

        var merchant_gateway = selectN({
            //元素容器【必填】
            elem: '#merchant_gateway'
            ,name:'merchant_gateway'
            ,verify:'required'
            ,selected:mg_selected
            //候选数据【必填】
            ,data: "{{route('merchant-gateway')}}"
            ,field:{
                idName: 'id',
                titleName: 'name',
                childName: 'gateways'
            }
        });


    })
</script>