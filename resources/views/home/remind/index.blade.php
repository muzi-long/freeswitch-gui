@extends('home.base')

@section('content')
    <div class="layui-card">

        <div class="layui-card-body">
            <div class="layui-row layui-col-space30">
                <div class="layui-col-xs6">
                    <div class="layui-card">
                        <div class="layui-card-header"><b>今日待跟进客户</b></div>
                        <div class="layui-card-body">
                            <table id="dataTable1" lay-filter="dataTable1"></table>
                        </div>
                    </div>
                </div>
                <div class="layui-col-xs6">
                    <div class="layui-card-header"><b>超期待跟进客户</b></div>
                    <div class="layui-card-body">
                        <table id="dataTable2" lay-filter="dataTable2"></table>
                    </div>
                </div>
            </div>
            <div class="layui-row layui-col-space30">
                <div class="layui-card">
                    <div class="layui-card-header"><b>节点占比</b></div>
                    <div class="layui-card-body">
                        <div id="node-pie" style="width: 100%;height: 400px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.extend({
            echarts: 'lib/extend/echarts' ,
            echartsTheme: 'lib/extend/echartsTheme' ,
        }).use(['layer','table','form','echarts','echartsTheme'],function () {
            var $ = layui.jquery;
            var layer = layui.layer;
            var form = layui.form;
            var table = layui.table;
            var echarts = layui.echarts;
            var echartsTheme = layui.echartsTheme;
            var myechart = echarts.init(document.getElementById('node-pie'),echartsTheme);
            $.post("{{route('home.remind.count')}}",{},function(res) {
                if (res.code==0){
                    var legend = [];
                    var series = [];
                    $.each(res.data,function(index,elem) {
                        legend.push(index);
                        series.push({
                            value:elem,
                            name:index
                        })
                    })
                    myechart.setOption({
                        title: {text: "各节点项目分布情况", x: "center", textStyle: {fontSize: 14}},
                        tooltip: {trigger: "item", formatter: "{a} <br/>{b} : {c} ({d}%)"},
                        legend: {orient: "vertical", x: "left", data: legend},
                        series: [{
                            name: "项目数",
                            type: "pie",
                            radius: "55%",
                            center: ["50%", "50%"],
                            data: series
                        }]
                    });
                }
            });
            window.onresize = myechart.resize;

            //用户表格初始化
            var dataTable1 = table.render({
                elem: '#dataTable1'
                ,height: 400
                ,url: "{{ route('home.remind.data',['type'=>1]) }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'company_name', title: '公司名称'}
                    ,{field: 'name', title: '姓名'}
                    ,{field: 'phone', title: '联系电话'}
                    ,{field: 'follow_at', title: '最近跟进时间',templet:function (d) {
                            return '<span style="color:green">'+d.follow_at+'</span>'
                        }}
                ]]
            });

            //用户表格初始化
            var dataTable2 = table.render({
                elem: '#dataTable2'
                ,height: 400
                ,url: "{{ route('home.remind.data',['type'=>2]) }}" //数据接口
                ,page: true //开启分页
                ,cols: [[ //表头
                    {checkbox: true,fixed: true}
                    ,{field: 'company_name', title: '公司名称'}
                    ,{field: 'name', title: '姓名'}
                    ,{field: 'phone', title: '联系电话'}
                    ,{field: 'next_follow_at', title: '下次跟进时间',templet:function (d) {
                            return '<span style="color:red">'+d.next_follow_at+'</span>'
                        }}
                ]]
            });

        })
    </script>
@endsection