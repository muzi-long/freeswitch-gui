<script>
    layui.use(['layer','table','form','element'],function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;
        var table = layui.table;
        var element = layui.element;

        form.on('switch(visiable)', function(data){
            console.log(data.elem); //得到checkbox原始DOM对象
            console.log(data.elem.checked); //开关是否开启，true或者false
            console.log(data.value); //开关value值，也可以通过data.elem.value得到
            console.log(data.othis); //得到美化后的DOM对象
            if (data.elem.checked){
                $(data.elem).val(1)
            }else {
                $(data.elem).val(2)
            }
        });

    });
</script>