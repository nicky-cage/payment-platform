<div class="layui-row">
    <div class="layui-col-md12">
        <div class="layui-card no-shadow">
            <div class="layui-card-body">
                <table class="layui-table">
                    <colgroup>
                        <col width="100"/>
                        <col width="100"/>
                        <col width="100"/>
                        <col width="100"/>
                        <col width="90"/>
                        <col width="90"/>
                        <col width="90"/>
                        <col width="150"/>
                        <col width="150"/>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>商户名称</th>
                        <th>统计日期</th>
                        <th>支付金额</th>
                        <th>支付笔数</th>
                        <th>支付手续费</th>
                        <th>代付金额</th>
                        <th>代付笔数</th>
                        <th>代付手续费</th>
                        <th>代理佣金</th>
                    </tr>
                    </thead>
                    <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('agent.report_days._list')</tbody>
                </table>
            </div>
        </div>
    </div>
</div>