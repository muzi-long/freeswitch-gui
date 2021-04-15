<div id="LAY-auth-tree-index"></div>

<script>
    layui.config({
        base: '/layuiadmin/' //静态资源所在路径
    }).extend({
        authtree: 'modules/authtree',
    }).use(['jquery', 'authtree', 'form', 'layer'], function () {
        var $ = layui.jquery;
        var authtree = layui.authtree;
        var form = layui.form;
        var layer = layui.layer;
        // 一般来说，权限数据是异步传递过来的
        $.ajax({
            method: 'post',
            url: '{{route('api.getPermissionByRoleId',['role_id'=>$role_id??null])}}',
            dataType: 'json',
            success: function (res) {
                //var trees = res.data.trees;
                // 如果后台返回的不是树结构，请使用 authtree.listConvert 转换
                var trees = authtree.listConvert(res.data.trees, {
                    primaryKey: 'id'
                    , startPid: 0
                    , parentKey: 'parent_id'
                    , nameKey: 'display_name'
                    , valueKey: 'id'
                    , checkedKey: res.data.checkedId
                })
                authtree.render('#LAY-auth-tree-index', trees, {
                    inputname: 'permission_ids[]',
                    layfilter: 'lay-check-auth',
                    autowidth: true,
                    autoclose: false
                });
            }
        });
    });
</script>
