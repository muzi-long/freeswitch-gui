<div class="layui-btn-group">
    <a href="{{route('frontend.crm.project')}}" class="layui-btn layui-btn-sm layui-btn-primary">返回列表</a>
    @can('frontend.crm.project.show')
    <a href="{{route('frontend.crm.project.show',['id'=>$model->id])}}" class="layui-btn layui-btn-sm">项目详情</a>
    @endcan
    @can('frontend.crm.project.follow')
    <a id="follow" class="layui-btn layui-btn-sm">跟进</a>
    @endcan
    @can('frontend.crm.project.destroy')
    <a id="destroyBtn" class="layui-btn layui-btn-sm layui-btn-danger">删除</a>
    @endcan
</div>
