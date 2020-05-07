<script>
    layui.use(['layer','table','form','element'],function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;
        var table = layui.table;
        var element = layui.element;

        form.on('switch(visiable)', function(data){
            if (data.elem.checked){
                $(data.elem).val(1)
            }else {
                $(data.elem).val(2)
            }
        });

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

    });
</script>