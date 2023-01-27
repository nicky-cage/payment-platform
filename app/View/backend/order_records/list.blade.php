@extends('backend._layouts.list')

@section('content')
@component('backend._slots.panel')
<form class="layui-form" lay-filter="" tbody="0">
    <div class="layui-form layui-card-header layuiadmin-card-header-auto">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">交易单号</label>
                <div class="layui-input-inline">
                    <input type="text" name="trade_number" placeholder="请输入交易单号" autocomplete="off" class="layui-input" />
                </div>
            </div>
            <div class="layui-inline">
                <button class="layui-btn" lay-submit lay-filter="sp-form-search">
                    <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                </button>
            </div>
        </div>
    </div>
</form>
@endcomponent

@component('backend._slots.panel')
<div class="layui-card-body">
    <table class="layui-table">
        <colgroup>
            <col width="60" />
            <col width="80" />
            <col width="120" />
            <col width="80" />
            <col width="160" />
            <col width="160" />
            <col width="80" />
            <col width="80" />
            <col width="100" />
            <col width="130" />
            <col width="130" />
            <col width="130" />
            <col />
            <col width="160"/>
        </colgroup>
        <thead>
            <tr>
                <th>序号</th>
                <th>商户名称</th>
                <th>应用名称</th>
                <th>支付名称</th>
                <th>交易单号(下游)</th>
                <th>交易单号(上游)</th>
                <th>交易金额</th>
                <th>实付金额</th>
                <th>状态</th>
                <th>下单时间</th>
                <th>确认时间(上游)</th>
                <th>确认时间(下游)</th>
                <th>备注</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('backend.order_records._list')</tbody>
    </table>
</div>
@endcomponent
<script>
    layui.use(['form', 'jquery', 'layer'], function() {
        let $ = layui.jquery;
        // 下发异步通知
        $(document).on("click", ".notify-send", function() { 
            let that = $(this), order_id = that.attr("order_id");
            $.post("/order_records/notify", { "order_id": order_id }, function(result) { 
                if (result.code != 0) { 
                    sp.alert(result.message);
                    return;
                }
                sp.alertSuccess('回调成功', function() { 
                    location.reload();
                });
            });
        });

        // 补单
        $(document).on("click", ".fix-order", function() { 
            let that = $(this), order_id = that.attr("order_id");
            $.post("/order_records/fix_order", { "order_id": order_id }, function(result) { 
                if (result.code != 0) { 
                    sp.alert(result.message);
                    return;
                }
                sp.alertSuccess('回调成功', function(result) { 
                    console.log(result);
                });
            });
        });

        // 查单
        $(document).on("click", ".query-order", function() { 
            let that = $(this), order_id = that.attr("order_id");
            layer.confirm('確定要补单吗?', { icon: 3, title: '提示' }, function(index) {
                $.post("/order_records/query_order", { "order_id": order_id }, function(result) { 
                    if (result.code != 0) { 
                        sp.alert(result.message);
                        return;
                    }
                });
                // layer.close(index);
            });
        });

    });
</script>
@endsection
