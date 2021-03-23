<script>
    layui.use(['layer','table','form','element','upload','laydate'],function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;
        var table = layui.table;
        var element = layui.element;
        var upload = layui.upload;
        var laydate = layui.laydate;


        //单图片上传
        window.removePic = function(obj,elem_ul,elem_input){
            $(elem_ul).html("")
            $("#"+elem_input).val("");
        }
        $(".uploadPic").each(function (index,elem) {
            var elem_ul = $(elem).data('ul')
            var elem_input = $(elem).data('input')
            upload.render({
                elem: $(elem)
                ,url: '{{ route("api.upload") }}'
                ,multiple: false
                ,data:{"_token":"{{ csrf_token() }}"}
                ,done: function(res){
                    if(res.code == 0){
                        layer.msg(res.msg,{icon:1},function () {
                            $(elem).parent('.layui-upload').find('.layui-upload-box').html('<li><img src="'+res.data.url+'" /><p onclick="removePic(this,\''+elem_ul+'\',\''+elem_input+'\')">删除</p></li>');
                            $(elem).parent('.layui-upload').find('.layui-upload-input').val(res.data.url);
                        })
                    }else {
                        layer.msg(res.msg,{icon:2})
                    }
                }
            });
        })

        //多图片
        window.removePics = function(obj,elem_ul,elem_input){
            $(obj).parent("li").remove()
            let pic_urls = []
            $("#"+elem_ul+" li").each(function (index,elem) {
                pic_urls.push($(elem).find("img").attr("src"))
            })
            console.log(pic_urls)
            $("#"+elem_input).val(pic_urls.join(','));
        }
        $(".uploadPics").each(function (index,elem) {
            var elem_ul = $(elem).data('ul')
            var elem_input = $(elem).data('input')
            upload.render({
                elem: $(elem)
                ,url: '{{ route("api.upload") }}'
                ,multiple: true
                ,data:{"_token":"{{ csrf_token() }}"}
                ,done: function(res){
                    if(res.code == 0){
                        layer.msg(res.msg,{icon:1},function () {
                            $("#"+elem_ul).append('<li><img src="'+res.data.url+'" /><p onclick="removePics(this,\''+elem_ul+'\',\''+elem_input+'\')">删除</p></li>');
                            let pic_urls = []
                            $("#"+elem_ul+" li").each(function (index,elem) {
                                pic_urls.push($(elem).find("img").attr("src"))
                            })
                            $("#"+elem_input).val(pic_urls.join(','));
                        })
                    }else {
                        layer.msg(res.msg,{icon:2})
                    }
                }
            });
        })


    });
</script>
