<script>
    layui.use(['layer','table','form','laydate'],function () {
        var layer = layui.layer;
        var form = layui.form;
        var table = layui.table;
        var laydate = layui.laydate;

        laydate.render({
            elem: '#expires_at',
            type: 'datetime'
        });

    })
</script>