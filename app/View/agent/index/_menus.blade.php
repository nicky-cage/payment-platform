<div class="layui-side layui-side-menu">
    <div class="layui-side-scroll">
        <div class="layui-logo" lay-href="home/console.html">
            <span>综合支付 - 后台管理</span>
        </div>
        <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
            @foreach ($menus as $key=>$menu)
            <li data-name="@if ($menu['id']==1000) home @else template @endif " class="layui-nav-item  @if ($menu['id']==1000) layui-nav-itemed @else  @endif">
                <a href="javascript:;" lay-tips="{{$menu['title']}}" lay-direction="2">
                    <i class="layui-icon {{$menu['icon']}}"></i>
                    <cite>{{$menu['title']}}</cite>
                </a>
                <dl class="layui-nav-child">
                    @foreach ($menu['children'] as $children)
                    <dd @if ($menu['id']==1000) data-name="console" class="layui-this" @endif>
                        <a lay-href="{{$children['url']}}">{{$children['title']}}</a>
                    </dd>
                    @endforeach
                </dl>
            </li>
            @endforeach
        </ul>
    </div>
</div>

<script>
    let MAX_TAB_NUM = 8; //最大允许的tab数量
    layui.config({
        base: '/static/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'element', 'jquery'], function() {
        let element = layui.element,
            $ = layui.jquery;
        let tabs_container = "layadmin-layout-tabs"; //顶级tabs容器
        element.on("nav", function(data) {
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