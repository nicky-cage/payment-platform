@extends('backend._layouts.list')

@section('content')
@component('backend._slots.panel')
<form class="layui-form" lay-filter="" tbody="0">
    <div class="layui-form layui-card-header layuiadmin-card-header-auto">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">商户名称</label>
                <div class="layui-input-inline">
                    <input type="text" name="merchant_name" placeholder="请输入商户名称" autocomplete="off" class="layui-input" />
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
<div class="layui-card-body">
    <button class="layui-btn sp-open-link" url="/{{$controller}}/create" area="880px,650px">新增账号</button>
</div>
@endcomponent

@component('backend._slots.panel')
<div class="layui-card-body">
    <table class="layui-table">
        <colgroup>
            <col width="60" />
            <col width="100" />
            <col width="120" />
            <col width="90" />
            <col width="90" />
            <col width="120" />
            <col />
            <col />
            <col width="60" />
            <col width="135" />
            <col width="150" />
        </colgroup>
        <thead>
            <tr>
                <th>序号</th>
                <th>商户编号</th>
                <th>商户名称</th>
                <th>结算金额</th>
                <th>手续费</th>
                <th>实际结算金额</th>
                <th>银行卡号/姓名</th>
                <th>省/市/县(区)</th>
                <th>状态</th>
                <th>完成时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('backend.merchant_settles._list')</tbody>
    </table>
</div>
@endcomponent
@endsection