<div id="xm-select-node" class="xm-select-node"></div>
<script>
    layui.use(['jquery','form', 'layer'], function () {
        var $ = layui.jquery;
        var form = layui.form;
        var layer = layui.layer;
        // 一般来说，权限数据是异步传递过来的
        $.ajax({
            method: 'post',
            url: '/api/get_node?node_id={{$node_id}}&type={{$type}}',
            dataType: 'json',
            success: function (res) {
                var demo1 = xmSelect.render({
                    el: '#xm-select-node',
                    name: 'node_id',
                    filterable: false,
                    radio: true,
                    clickClose: true,
                    model: { label: { type: 'text' } },
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
