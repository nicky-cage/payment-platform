<div class="layui-card-body">
    <table class="layui-table">
        <colgroup>
            <col width="60" />
            <col />
            <col width="100" />
            <col width="100" />
            <col width="90" />
            <col width="90" />
            <col width="90" />
            <col width="150" />
            <col width="150" />
        </colgroup>
        <thead>
            <tr>
                <th>序号</th>
                <th>商户名称</th>
                <th>统计年份</th>
                <th>支付方式</th>
                <th>平台收入</th>
                <th>平台成本</th>
                <th>平台利润</th>
                <th>成功金额/笔数</th>
                <th>失败金额/笔数</th>
            </tr>
        </thead>
        <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list"></tbody>
    </table>
</div>