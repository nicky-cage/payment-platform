<div class="layui-side layui-side-menu">
    <div class="layui-side-scroll">
        <div class="layui-logo" lay-href="home/console.html">
            <span>平台管理</span>
        </div>
        <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
            <li data-name=" home  " class="layui-nav-item">
                <a href="javascript:;" lay-tips="快捷访问" lay-direction="2">
                    <i class="layui-icon layui-icon-home"></i>
                    <cite>快捷访问</cite>
                    <span class="layui-nav-more"></span></a>
                <dl class="layui-nav-child">
                    <dd data-name="console" class="">
                        <a lay-href="/index/right">后台首页</a>
                    </dd>
                </dl>
            </li>
            <li data-name="template " class="layui-nav-item">
                <a href="javascript:;" lay-tips="银行收款" lay-direction="2">
                    <i class="layui-icon layui-icon-set"></i>
                    <cite>银行收款</cite>
                    <span class="layui-nav-more"></span></a>
                <dl class="layui-nav-child">
                    <dd class=""><a lay-href="/cards/list">收款管理</a></dd>
                    <dd class=""><a lay-href="/merchant_cards/list">收款额度</a></dd>
                    <dd class=""><a lay-href="/card_records/list">收款记录</a></dd>
                </dl>
            </li>
            <li data-name="template " class="layui-nav-item">
                <a href="javascript:;" lay-tips="支付管理" lay-direction="2">
                    <i class="layui-icon layui-icon-rmb"></i>
                    <cite>支付管理</cite>
                    <span class="layui-nav-more"></span></a>
                <dl class="layui-nav-child">
                    <dd class=""><a lay-href="/orders/list">支付订单</a></dd>
                    <dd class=""><a lay-href="/order_records/list">支付记录</a></dd>
                    <dd class=""><a lay-href="/notify_ups/list">上游通知</a></dd>
                    <dd class=""><a lay-href="/notify_downs/list">下游通知</a></dd>
                </dl>
            </li>
            <li data-name="template " class="layui-nav-item">
                <a href="javascript:;" lay-tips="提现管理" lay-direction="2">
                    <i class="layui-icon layui-icon-dollar"></i>
                    <cite>提现管理</cite>
                    <span class="layui-nav-more"></span></a>
                <dl class="layui-nav-child">
                    <dd class=""><a lay-href="/payouts/list">申请提现</a></dd>
                    <dd class=""><a lay-href="/payout_records/list">提现记录</a></dd>
                    <dd class=""><a lay-href="/notify_ups/list">上级通知</a></dd>
                    <dd class=""><a lay-href="/notify_downs/list">下级通知</a></dd>
                </dl>
            </li>
            <li data-name="template " class="layui-nav-item">
                <a href="javascript:;" lay-tips="商户管理" lay-direction="2">
                    <i class="layui-icon layui-icon-group"></i>
                    <cite>商户管理</cite>
                    <span class="layui-nav-more"></span></a>
                <dl class="layui-nav-child">
                    <dd class=""><a lay-href="/merchants/list">商户信息</a></dd>
                    <dd class=""><a lay-href="/merchant_apps/list">商户应用</a></dd>
                </dl>
            </li>
            <li data-name="template " class="layui-nav-item">
                <a href="javascript:;" lay-tips="账户管理" lay-direction="2">
                    <i class="layui-icon layui-icon-user"></i>
                    <cite>账户管理</cite>
                    <span class="layui-nav-more"></span></a>
                <dl class="layui-nav-child">
                    <dd class=""><a lay-href="/merchant_accounts/list">商户账户</a></dd>
                    <dd class=""><a lay-href="/merchant_channels/list">商户账变</a></dd>
                </dl>
            </li>
            <li data-name="template " class="layui-nav-item">
                <a href="javascript:;" lay-tips="支付通道" lay-direction="2">
                    <i class="layui-icon layui-icon-auz"></i>
                    <cite>支付通道</cite>
                    <span class="layui-nav-more"></span></a>
                <dl class="layui-nav-child">
                    <dd class=""><a lay-href="/channels/list">支付渠道</a></dd>
                    <dd class=""><a lay-href="/channel_payments/list">支付方式</a></dd>
                    <dd class=""><a lay-href="/channel_up_streams/list">上游通道</a></dd>
                    <dd class=""><a lay-href="/channel_configs/list">通道规则</a></dd>
                    <dd class=""><a lay-href="/channel_products/list">支付产品</a></dd>
                    <dd class=""><a lay-href="/channel_down_streams/list">下游代付</a></dd>
                </dl>
            </li>
            <li data-name="template " class="layui-nav-item">
                <a href="javascript:;" lay-tips="数据管理" lay-direction="2">
                    <i class="layui-icon layui-icon-util"></i>
                    <cite>数据管理</cite>
                    <span class="layui-nav-more"></span></a>
                <dl class="layui-nav-child">
                    <dd class=""><a lay-href="/operation_logs/list">操作日志</a></dd>
                    <dd class=""><a lay-href="/error_logs/list">错误日志</a></dd>
                    <dd class=""><a lay-href="/admin_tools/clear">数据清理</a></dd>
                </dl>
            </li>
            <li data-name="template " class="layui-nav-item">
                <a href="javascript:;" lay-tips="统计报表" lay-direction="2">
                    <i class="layui-icon layui-icon-table"></i>
                    <cite>统计报表</cite>
                    <span class="layui-nav-more"></span></a>
                <dl class="layui-nav-child">
                    <!--<dd class="">
                        <a lay-href="/report_real_times/list">实时统计</a>
                    </dd>-->
                    <dd class=""><a lay-href="/report_days/list">平台报表</a></dd>
                    <!--  <dd class="">
                          <a lay-href="/report_channels/list">渠道额度</a>
                      </dd>-->
                </dl>
            </li>
            <li data-name="template " class="layui-nav-item">
                <a href="javascript:;" lay-tips="系统管理" lay-direction="2">
                    <i class="layui-icon layui-icon-windows"></i>
                    <cite>系统管理</cite>
                    <span class="layui-nav-more"></span></a>
                <dl class="layui-nav-child">
                    <dd class="layui-this"><a lay-href="/admins/list">系统账号</a></dd>
                    <dd class=""><a lay-href="/permission_ips/list">访问授权</a></dd>
                    <dd class=""><a lay-href="/admin_login_logs/list">登录日志</a></dd>
                </dl>
            </li>
            <span class="layui-nav-bar" style="top: 476px; height: 0px; opacity: 0;"></span>
        </ul>
    </div>
</div>

<script>
    let MAX_TAB_NUM = 8; //最大允许的tab数量
    layui.config({base: '/static/layuiadmin/'})
        .extend({index: 'lib/index'})
        .use(['index', 'element', 'jquery'], function () {
            let element = layui.element,
                $ = layui.jquery;
            let tabs_container = "layadmin-layout-tabs"; //顶级tabs容器
            element.on("nav", function (data) {
                let tabs = $("div[lay-filter=" + tabs_container + "] li");
                let over_count = tabs.length - MAX_TAB_NUM; //超出的tab的数量
                if (over_count >= 0) {
                    for (let i = 0; i < over_count; i++) {
                        let tab = tabs.eq(i + 1);
                        let id = tab.attr("lay-id");
                        if (id) {
                            element.tabDelete(tabs_container, id);
                        }
                    }
                }
            });
        });
</script>
