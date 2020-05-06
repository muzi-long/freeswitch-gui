<script>
    layui.use(['layer','table','form','jquery'],function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;
        var table = layui.table;

        form.on('submit(*)',function (data) {
            var load = layer.load();
            $.post(data.form.action,data.field,function (res) {
                layer.close(load);
                layer.msg(res.msg,{time:2000},function () {
                    if (res.code==0){
                        parent.location.reload();
                    }
                })
            });
            return false;
        })

    });
</script>