
<div id="xm-select-role" class="xm-select-role"></div>
<script>
    layui.use(['jquery','form', 'layer'], function () {
        var $ = layui.jquery;
        var form = layui.form;
        var layer = layui.layer;
        // 一般来说，权限数据是异步传递过来的
        $.ajax({
            method: 'post',
            url: '{{route('api.getRoleByUserId',['user_id'=>$user_id??0])}}',
            dataType: 'json',
            success: function (res) {
                var demo1 = xmSelect.render({
                    el: '#xm-select-role',
                    name: 'role_ids',
                    prop: {
                        name: 'name',
                        value: 'id',
                    },
                    data: res.data,
                })

            }
        });
    });
</script>
