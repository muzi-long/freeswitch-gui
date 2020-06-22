<script>
    layui.use(['element','form','jquery','layer'],function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;
        form.on('submit(go)',function (data) {
            var load = layer.load();
            $.post(data.form.action,data.field,function (res) {
                layer.close(load);
                layer.msg(res.msg,{icon:res.code==0?1:2},function () {
                    if (res.code==0){
                        location.reload();
                    }
                })
            });
            return false;
        })
    })
</script>