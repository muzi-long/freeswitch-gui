<script>
    layui.use(['layer','table','form','element','upload','laydate'],function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;
        var table = layui.table;
        var element = layui.element;
        var upload = layui.upload;
        var laydate = layui.laydate;

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
        //删除
        $("#destroyBtn").click(function () {
            layer.confirm('删除后客户将进入公海库，所有人可拾回。确认删除吗？', function(index){
                layer.closeAll();
                var load = layer.load();
                $.post("{{ route('frontend.crm.project.destroy') }}",{_method:'delete',ids:["{{$model->id}}"]},function (res) {
                    layer.close(load);
                    if (res.code == 0) {
                        layer.msg(res.msg, {icon: 1}, function () {
                            location.href = "{{route('frontend.crm.project')}}";
                        })
                    } else {
                        layer.msg(res.msg, {icon: 2})
                    }
                });
            });
        });

        //节点进度
        var dataTableFollow = table.render({
            elem: '#dataTableFollow'
            ,height: '480'
            ,url: "{{route('frontend.crm.project.followList',['id'=>$model->id])}}"
            ,page: true
            ,toolbar: false
            ,cols: [[
                {field: 'old_node_name', title: '原节点'}
                ,{field: 'new_node_name', title: '新节点'}
                ,{field: 'created_at', title: '跟进时间'}
                ,{field: 'next_follow_at', title: '下次跟进时间'}
                ,{field: 'staff_id', title: '跟进人',templet:function (d) {
                        return d.staff.nickname;
                    }}
                ,{field: 'content', title: '备注'}
            ]]
        });

        $("#follow").click(function () {
            layer.open({
                type:2,
                title:'跟进客户',
                shadeClose:true,
                area:['600px','600px'],
                content:'{{route('frontend.crm.project.follow',['id'=>$model->id])}}'
            });
        });

        form.on('submit(go_parent)',function (data) {
            var load = layer.load();
            $.post(data.form.action,data.field,function (res) {
                layer.close(load);
                layer.msg(res.msg,{icon:res.code==0?1:2},function () {
                    if (res.code==0 && res.url){
                        parent.location.reload();
                        return;
                    }
                    if (res.code==0 && res.refresh){
                        parent.location.reload();
                        return
                    }
                })
            });
            return false;
        })

        @endif
    });
</script>
