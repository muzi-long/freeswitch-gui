<script>
    layui.use(['element','form','jquery','layer','laydate'],function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;
        var laydate = layui.laydate;

        laydate.render({
            elem: '#expire_at',
            type: 'datetime'
        });
    })
</script>