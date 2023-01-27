@extends('backend._layouts.list')

@section('content')
@component('backend._slots.panel')
<form class="layui-form" lay-filter="" tbody="0">
    <div class="layui-form layui-card-header layuiadmin-card-header-auto">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">商户编号</label>
                <div class="layui-input-inline">
                    <input type="text" name="merchant_id" placeholder="请输入商户编号" autocomplete="off" class="layui-input" />
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">应用编号</label>
                <div class="layui-input-inline">
                    <input type="text" name="app_id" placeholder="请输入应用编号" autocomplete="off" class="layui-input" />
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
            <col width="120" />
            <col width="150" />
            <col width="160" />
            <col />
            <col width="250" />
            <col width="80" />
            <col width="80" />
            <col width="130" />
        </colgroup>
        <thead>
            <tr>
                <th>序号</th>
                <th>商户编号/名称</th>
                <th>应用编号/名称</th>
                <th>订单编号 - 下游</th>
                <th>通知URL</th>
                <th>下游回复</th>
                <th>状态</th>
                <th>失败次数</th>
                <th>通知时间</th>
            </tr>
        </thead>
        <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('backend.notify_downs._list')</tbody>
    </table>
</div>
@endcomponent
@endsection