<script>
    layui.use(['layer','table','form','element','upload','laydate'],function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;
        var table = layui.table;
        var element = layui.element;
        var upload = layui.upload;
        var laydate = layui.laydate;


        //节点进度
        var dataTableNode = table.render({
            elem: '#dataTableNode'
            ,height: '480'
            ,url: "{{route('admin.project.nodeList',['id'=>$model->id])}}"
            ,page: true
            ,toolbar: false
            ,cols: [[
                {type: 'checkbox'}
                ,{field: 'old', title: '原节点',templet:function (d) {
                        return d.old_node.name;
                    }}
                ,{field: 'new', title: '新节点',templet:function (d) {
                        return d.new_node.name;
                    }}
                ,{field: 'content', title: '备注'}
                ,{field: 'username', title: '操作人',templet:function (d) {
                        return d.user.nickname;
                    }}
                ,{field: 'created_at', title: '操作时间'}
            ]]
        });

        //备注进度
        var dataTableRemark = table.render({
            elem: '#dataTableRemark'
            ,height: '480'
            ,url: "{{route('admin.project.remarkList',['id'=>$model->id])}}"
            ,page: true
            ,toolbar: false
            ,cols: [[
                {type: 'checkbox'}
                ,{field: 'content', title: '备注'}
                ,{field: 'username', title: '跟进人',templet:function (d) {
                        return d.user.nickname;
                    }}
                ,{field: 'created_at', title: '跟进时间'}
                ,{field: 'next_follow_at', title: '下次跟进时间'}
            ]]
        });

        $("#retrieve").click(function () {
            layer.confirm('确认拾回吗？', function(index){
                layer.close(index);
                var load = layer.load();
                $.post("{{ route('admin.waste.retrieve') }}",{id:{{$model->id}}},function (res) {
                    layer.close(load);
                    if (res.code == 0) {
                        layer.msg(res.msg, {icon: 1}, function () {
                            location.href='{{route('admin.waste')}}'
                        })
                    } else {
                        layer.msg(res.msg, {icon: 2})
                    }
                });
            });
        })

    });
</script>
