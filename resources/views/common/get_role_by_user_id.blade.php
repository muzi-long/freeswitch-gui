
<div id="xm-select-role" class="xm-select-role"></div>
<input type="hidden" name="role_ids" id="role_ids">
<script src="/layuiadmin/xm-select.js"></script>
<script>
    layui.use(['jquery','form', 'layer'], function () {
        var $ = layui.jquery;
        var form = layui.form;
        var layer = layui.layer;
        // 一般来说，权限数据是异步传递过来的
        $.ajax({
            method: 'post',
            url: '{{route('api.getRoleByUserId',['user_id'=>$user_id??null])}}',
            dataType: 'json',
            success: function (res) {
                var role_ids = []
                var demo1 = xmSelect.render({
                    el: '#xm-select-role',
                    data: res.data,
                    on: function (data) {
                        role_ids = []
                        data.arr.forEach(function (item) {
                            role_ids.push(item.value)
                        })
                        document.getElementById("role_ids").value = role_ids.join(",")
                    }
                })
                demo1.getValue().forEach(function (item) {
                    role_ids.push(item.value)
                })
                document.getElementById("role_ids").value = role_ids.join(",")
            }
        });
    });
</script>
