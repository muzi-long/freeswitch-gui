<script>
    layui.use(['layer','table','form','laydate'],function () {
        var layer = layui.layer;
        var form = layui.form;
        var table = layui.table;
        var laydate = layui.laydate;

        laydate.render({elem:'#date_start',type:'date'});
        laydate.render({elem:'#date_end',type:'date'});
        laydate.render({elem:'#time_start',type:'time'});
        laydate.render({elem:'#time_end',type:'time'});

    })
</script>