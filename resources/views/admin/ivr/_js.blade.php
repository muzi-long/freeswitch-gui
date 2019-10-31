<script>
    layui.use(['layer','table','form'],function () {
        var layer = layui.layer;
        var form = layui.form;
        var table = layui.table;

        $(".tts-btn").click(function () {
            layer.open({
                type:2,
                title:'语音在线合成',
                area:['80%','80%'],
                shadeClose:true,
                content:"{{route('admin.audio')}}"
            })
        })

    })
</script>