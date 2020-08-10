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

        form.on('switch(required)', function(data){
            if (data.elem.checked){
                $(data.elem).val(1)
            }else {
                $(data.elem).val(2)
            }
        });

    });
</script>
