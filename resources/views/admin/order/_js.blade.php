<script>
    layui.use(['layer','table','form','element','upload','laydate'],function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;
        var table = layui.table;
        var element = layui.element;
        var upload = layui.upload;
        var laydate = layui.laydate;

        form.on('submit(go)',function (data) {
            var load = layer.load();
            $.post(data.form.action,data.field,function (res) {
                layer.close(load);
                layer.msg(res.msg,{icon:res.code==0?1:2},function () {
                    if (res.code==0){
                        location.reload();
                    }
                })
            });
            return false;
        })

        //图片
        $(".uploadPic").each(function (index,elem) {
            upload.render({
                elem: $(elem)
                ,url: '{{ route("api.upload") }}'
                ,multiple: false
                ,data:{"_token":"{{ csrf_token() }}"}
                ,done: function(res){
                    //如果上传失败
                    if(res.code == 0){
                        layer.msg(res.msg,{icon:1},function () {
                            $(elem).parent('.layui-upload').find('.layui-upload-box').html('<li><img src="'+res.url+'" /><p>上传成功</p></li>');
                            $(elem).parent('.layui-upload').find('.layui-upload-input').val(res.url);
                        })
                    }else {
                        layer.msg(res.msg,{icon:2})
                    }
                }
            });
        })

        laydate.render({
            elem: '#next_follow_at',
            type: 'datetime'
        });

        @if(isset($model))
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

        //删除
        $("#destroyBtn").click(function () {
            layer.confirm('删除后客户将进入公海库，所有人可拾回。确认删除吗？', function(index){
                layer.closeAll();
                var load = layer.load();
                $.post("{{ route('admin.order.destroy') }}",{_method:'delete',ids:["{{$model->id}}"]},function (res) {
                    layer.close(load);
                    if (res.code == 0) {
                        layer.msg(res.msg, {icon: 1}, function () {
                            location.href = "{{route('admin.order')}}";
                        })
                    } else {
                        layer.msg(res.msg, {icon: 2})
                    }
                });
            });
        });
        @endif
    });
</script>
