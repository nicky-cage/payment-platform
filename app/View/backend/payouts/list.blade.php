@extends('backend._layouts.list')

@section('content')
@component('backend._slots.panel')
<form class="layui-form" lay-filter="" tbody="0">
    <div class="layui-form layui-card-header layuiadmin-card-header-auto">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">订单编号</label>
                <div class="layui-input-inline">
                    <input type="text" name="order_number" placeholder="请输入订单编号" autocomplete="off" class="layui-input" />
                </div>
            </div>
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
            <col width="150" />
            <col width="150" />
            <col width="120" />
            <col width="150" />
            <col width="100" />
            <col width="100" />
            <col width="100" />
            <col width="130" />
            <col width="130" />
            <col />
            <col width="110" />
        </colgroup>
        <thead>
            <tr>
                <th>序号</th>
                <th>商户名称</th>
                <th>应用名称</th>
                <th>支付名称</th>
                <th>订单编号(下游)</th>
                <th>提现金额</th>
                <th>实付金额</th>
                <th>状态</th>
                <th>申请时间</th>
                <th>完成时间</th>
                <th>备注</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('backend.payouts._list')</tbody>
    </table>
</div>
@endcomponent
<script>
    layui.use(['jquery', 'layer'], function() { 
        let $ = layui.jquery, layer = layui.layer;

        $(document).on("click", ".set-success", function() { 
            let orderID = $(this).attr("order_id");
            sp.confirm("你确定已经完成用户提现操作?", function() { 
                $.post("/payouts/success",  {"order_id": orderID}, function(result) { 
                    if (result.code != 0) {
                        sp.alert(result.message);
                        return;
                    }
                    sp.alertSuccess("操作订单确认成功", function() { 
                        location.reload();
                    });
                });
            });
        });

        $(document).on("click", ".set-deny", function() { 
            let orderID = $(this).attr("order_id");
            sp.confirm("你确定要拒绝商户的提现申请么?", function() { 
                $.post("/payouts/deny", {"order_id": orderID}, function(result) { 
                    if (result.code != 0) { 
                        sp.alert(result.message);
                        return;
                    }
                    sp.alertSuccess("操作订单拒绝成功", function() { 
                        location.reload();
                    });
                });
            });
        });
    });
</script>
@endsection