<script>
    layui.use(['element','form','jquery','layer'],function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;

        form.on("select(merchant)",function (data) {
            var merchant_id = data.value;
            if (merchant_id){
                $.post('{{route('getGatewayByMerchantId')}}',{merchant_id:merchant_id},function (res) {
                    var _html = '<option value="0"></option>';
                    $.each(res.data,function (index,item) {
                        _html += '<option value="'+item.id+'">'+item.name+'</option>';
                    })

                    $("#gateway").html(_html);
                    form.render('select');

                })
            }
        })

    })
</script>
