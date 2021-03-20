
<div id="xm-select-department" class="xm-select-department"></div>
<script src="/layuiadmin/xm-select.js"></script>
<script>
    layui.use(['jquery','form', 'layer'], function () {
        var $ = layui.jquery;
        var form = layui.form;
        var layer = layui.layer;
        // 一般来说，数据是异步传递过来的
        $.ajax({
            method: 'post',
            url: '{{route('api.getDepartmentByUserId',['user_id'=>$user_id??null])}}',
            dataType: 'json',
            success: function (res) {
                var demo1 = xmSelect.render({
                    el: '#xm-select-department',
                    name: 'department_id',
                    model: { label: { type: 'text' } },
                    radio: true,
                    clickClose: true,
                    tree: {
                        show: true,
                        showLine: false,
                        strict: false,
                        expandedKeys: true,
                    },
                    data: res.data,
                })
            }
        });
    });
</script>
