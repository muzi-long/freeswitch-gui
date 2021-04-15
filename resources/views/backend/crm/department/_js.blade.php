<script>
    layui.use(['element','form','jquery'],function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;

        form.on("select(merchant)",function (data) {
            var merchant_id = data.value;
            if (merchant_id){
                $.post('{{route('getDepartmentByMerchantId')}}',{merchant_id:merchant_id},function (res) {
                    var _html = '<option value="0"></option>';
                    $.each(res.data,function (index,item) {
                        _html += '<option value="'+item.id+'">'+item.name+'</option>';
                        $.each(item.childs,function (index1,item1) {
                            _html += '<option value="'+item1.id+'">&nbsp;&nbsp;&nbsp;┗━━'+item1.name+'</option>';
                            $.each(item1.childs,function (index2,item2) {
                                _html += '<option value="'+item2.id+'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┗━━'+item2.name+'</option>';
                            })
                        })
                    })
                    $("#department").html(_html);
                    form.render('select');
                })
            }else{
                $("#department").html('<option value="'+item.id+'">'+item.name+'</option>');
                form.render('select');
            }

        })

    })
</script>
