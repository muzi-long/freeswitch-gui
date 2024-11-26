<div id="transfer-sips"></div>
<input type="hidden" name="sips" id="sips">
<script>
    layui.use(['jquery','form', 'layer','transfer'], function () {
        var $ = layui.jquery;
        var form = layui.form;
        var layer = layui.layer;
        var transfer = layui.transfer;
        // 一般来说，权限数据是异步传递过来的
        $.ajax({
            method: 'post',
            url: '/api/get_sips_by_queue_id?queue_id={{$queue_id}}',
            dataType: 'json',
            success: function (res) {
                transfer.render({
                    elem: '#transfer-sips'
                    ,id:'transfer-sips'
                    ,title: ['所有坐席', '已选坐席']
                    ,parseData: function(item){
                        return {
                            "value": item.id //数据值
                            ,"title": item.username + "（" + item.user.nickname + "）" //数据标题
                            //,"disabled": res.disabled  //是否禁用
                            ,"checked": res.checked //是否选中
                        }
                    }
                    ,data: res.data.lists
                    ,height: 300
                    ,value: res.data.values
                    ,onchange: function(data, index){
                        var ids = []
                        getData = transfer.getData('transfer-sips');
                        for (var v of getData){
                            ids.push(v.value)
                        }
                        $("#sips").val(ids.join(','))
                    }
                })
                $("#sips").val(res.data.values.join(','))

            },

        });
    });
</script>
