<script>
    layui.config({
        base: '/static/admin/layuiadmin/modules/'
    }).extend({
        selectN: 'select-ext/selectN'
    }).use(['layer','table','form','selectN'],function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;
        var table = layui.table;
        var selectN = layui.selectN;

    })
</script>