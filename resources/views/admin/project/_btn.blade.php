<div class="layui-btn-group">
    <a href="{{route('admin.project')}}" class="layui-btn layui-btn-sm layui-btn-primary">返回列表</a>
    <a href="{{route('admin.project.show',['id'=>$model->id])}}" class="layui-btn layui-btn-sm">项目详情</a>
    <a href="{{route('admin.project.node',['id'=>$model->id])}}" class="layui-btn layui-btn-sm">更新节点</a>
    <a href="{{route('admin.project.remark',['id'=>$model->id])}}" class="layui-btn layui-btn-sm">添加备注</a>
    <a id="destroyBtn" class="layui-btn layui-btn-sm layui-btn-danger">删除</a>
</div>