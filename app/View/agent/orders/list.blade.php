@extends('agent._layouts.list')

@section('content')
@component('agent._slots.panel')
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

@component('agent._slots.panel')
<div class="layui-card-body">
    <table class="layui-table">
        <colgroup>
            <col width="60" />
            <col width="200" />
            <col width="200" />
            <col width="100" />
            <col width="150" />
            <col width="180" />
            <col width="80" />
            <col width="80" />
            <col width="100" />
            <col width="135" />
            <col width="135" />
            <col />
        </colgroup>
        <thead>
            <tr>
                <th>序号</th>
                <th>商户编号/名称</th>
                <th>应用编号/名称</th>
                <th>支付名称</th>
                <th>订单编号(下游)</th>
                <th>交易单号(上游)</th>
                <th>交易金额</th>
                <th>实付金额</th>
                <th>状态</th>
                <th>交易时间</th>
                <th>完成时间</th>
                <th>备注</th>
            </tr>
        </thead>
        <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('agent.orders._list')</tbody>
    </table>
</div>
@endcomponent
@endsection