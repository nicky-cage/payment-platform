@extends('agent._layouts.base')

@section('content')
    <!--
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md8">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md6">
                    <div class="layui-card">
                        <div class="layui-card-header">快捷方式</div>
                        <div class="layui-card-body">

                            <div class="layui-carousel layadmin-carousel layadmin-shortcut">
                                <div carousel-item>
                                    <ul class="layui-row layui-col-space10">
                                        <li class="layui-col-xs3">
                                            <a lay-href="home/homepage1.html">
                                                <i class="layui-icon layui-icon-console"></i>
                                                <cite>会员管理</cite>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs3">
                                            <a lay-href="home/homepage2.html">
                                                <i class="layui-icon layui-icon-chart"></i>
                                                <cite>代理管理</cite>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs3">
                                            <a lay-href="component/layer/list.html">
                                                <i class="layui-icon layui-icon-template-1"></i>
                                                <cite>提款管理</cite>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs3">
                                            <a layadmin-event="im">
                                                <i class="layui-icon layui-icon-chat"></i>
                                                <cite>提款审核</cite>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs3">
                                            <a lay-href="component/progress/index.html">
                                                <i class="layui-icon layui-icon-find-fill"></i>
                                                <cite>存款管理</cite>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs3">
                                            <a lay-href="app/workorder/list.html">
                                                <i class="layui-icon layui-icon-survey"></i>
                                                <cite>代理提款</cite>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs3">
                                            <a lay-href="user/user/list.html">
                                                <i class="layui-icon layui-icon-user"></i>
                                                <cite>广告管理</cite>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs3">
                                            <a lay-href="set/system/website.html">
                                                <i class="layui-icon layui-icon-set"></i>
                                                <cite>站点设置</cite>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md6">
                    <div class="layui-card">
                        <div class="layui-card-header">待办事项</div>
                        <div class="layui-card-body">
                            <div class="layui-carousel layadmin-carousel layadmin-backlog">
                                <div carousel-item>
                                    <ul class="layui-row layui-col-space10">
                                        <li class="layui-col-xs6">
                                            <a lay-href="app/content/comment.html" class="layadmin-backlog-body">
                                                <h3>待审存款</h3>
                                                <p><cite>66</cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6">
                                            <a lay-href="app/forum/list.html" class="layadmin-backlog-body">
                                                <h3>待审提款</h3>
                                                <p><cite>12</cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6">
                                            <a lay-href="template/goodslist.html" class="layadmin-backlog-body">
                                                <h3>代理申请</h3>
                                                <p><cite>99</cite></p>
                                            </a>
                                        </li>
                                        <li class="layui-col-xs6">
                                            <a href="javascript:;" onclick="layer.tips('不跳转', this, {tips: 3});" class="layadmin-backlog-body">
                                                <h3>代理提款</h3>
                                                <p><cite>20</cite></p>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md12">
                    <div class="layui-card">
                        <div class="layui-card-header">数据概览</div>
                        <div class="layui-card-body">
                            <div class="layui-carousel layadmin-carousel layadmin-dataview" data-anim="fade" lay-filter="LAY-index-dataview">
                                <div carousel-item id="LAY-index-dataview">
                                    <div><i class="layui-icon layui-icon-loading1 layadmin-loading"></i></div>
                                    <div></div>
                                    <div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="layui-col-md4">
            <div class="layui-card">
                <div class="layui-card-header">版本信息</div>
                <div class="layui-card-body layui-text" style="padding-bottom: 28px;">
                    <table class="layui-table">
                        <colgroup>
                            <col width="100">
                            <col>
                        </colgroup>
                        <tbody>
                            <tr>
                                <td>当前版本</td>
                                <td> SHIPU V1.0.0 Beta1</td>
                            </tr>
                            <tr>
                                <td>最新版本</td>
                                <td> SHIPU V1.0.0 Beta1</td>
                            </tr>
                            <tr>
                                <td>开发框架</td>
                                <td>Gin/Layui/ES</td>
                            </tr>
                            <tr>
                                <td>更新时间</td>
                                <td>2020-07-20 12:30:40</td>
                            </tr>
                            <tr>
                                <td>技术支持</td>
                                <td>请优先联系相关运营人员</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="layui-card">
                <div class="layui-card-header">效果报告</div>
                <div class="layui-card-body layadmin-takerates">
                    <div class="layui-progress" lay-showPercent="yes">
                        <h3>转化率（日同比 28% <span class="layui-edge layui-edge-top" lay-tips="增长" lay-offset="-15"></span>）</h3>
                        <div class="layui-progress-bar" lay-percent="65%"></div>
                    </div>
                    <div class="layui-progress" lay-showPercent="yes">
                        <h3>签到率（日同比 11% <span class="layui-edge layui-edge-bottom" lay-tips="下降" lay-offset="-15"></span>）</h3>
                        <div class="layui-progress-bar" lay-percent="32%"></div>
                    </div>
                </div>
            </div>

            <div class="layui-card">
                <div class="layui-card-header">实时监控</div>
                <div class="layui-card-body layadmin-takerates">
                    <div class="layui-progress" lay-showPercent="yes">
                        <h3 id="cpu-info">CPU使用率</h3>
                        <div class="layui-progress-bar" lay-percent="50%" id="cpu-avg"></div>
                    </div>
                    <div class="layui-progress" lay-showPercent="yes">
                        <h3 id="mem-info">内存占用率</h3>
                        <div class="layui-progress-bar layui-bg-red" lay-percent="50%" id="mem-avg"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
-->
    <script>
        layui.config({
            base: '/static/layuiadmin/'
        })
            .extend({
                index: 'lib/index'
            })
            .use(['index', 'console', 'jquery', 'element'], function () {
                //let $ = layui.jquery, element = layui.element;
                ///** 以下为websocket相关处理 **/
                //let url = "ws://" + window.location.host + ":8910/ws"; //let url = "ws://" + window.location.host + "/ws";
                //let ws = new WebSocket(url);
                //let timerUpdateInfo, updateInfo = function() {
                //    let data = { code: 100101, data: "" };
                //    timerUpdateInfo = setInterval(function() {
                //        ws.send(JSON.stringify(data));
                //    }, 5000)
                //};
                //let cpuInfo = $("#cpu-info"), cpuAver = $("#cpu-avg"), memInfo = $("#mem-info"), memAver = $("#mem-avg"), showInfo = function(res) {
                //    let result = JSON.parse(res.data);
                //    let cpu = result.data.cpu, process = result.data.process;
                //    cpuInfo.text("CPU使用 (核心数量: " + cpu.total + ", 进程数量: " + process.total + ")");
                //    cpuAver.attr("lay-percent", cpu.avg + "%");
                //    let mem = result.data.memory;
                //    memInfo.text("内存占用(总计: " + mem.total + "G, 可用: " + mem.available + "G, 空闲: " + mem.free + "G)");
                //    memAver.attr("lay-percent", mem.percent + "%");
                //    element.render('progress');
                //};
                //ws.onopen = function() { updateInfo(); }; //建立建接之后开启刷新
                //ws.onmessage = function(res) { showInfo(res); }; //显示信息
                //ws.onclose = function() { //断开之后停止
                //    console.log("链接已接断开, 需要重新建立连接 ...");
                //    clearInterval(timerUpdateInfo);
                //    ws = new WebSocket(url);
                //};
            });
    </script>
@endsection