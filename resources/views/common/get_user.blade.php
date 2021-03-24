<div id="xm-select-user" class="xm-select-user"></div>
<script>
    layui.use(['jquery','form', 'layer'], function () {
        var $ = layui.jquery;
        var form = layui.form;
        var layer = layui.layer;
        // 一般来说，权限数据是异步传递过来的
        $.ajax({
            method: 'post',
            url: '{{route('api.getUser',['user_id'=>$user_id??0])}}',
            dataType: 'json',
            success: function (res) {
                var demo1 = xmSelect.render({
                    el: '#xm-select-user',
                    name: 'user_id',
                    filterable: true,
                    radio: true,
                    clickClose: true,
                    model: { label: { type: 'text' } },
                    prop: {
                        name: 'nickname',
                        value: 'id',
                    },
                    data: res.data,
                })

            }
        });
    });
</script>
