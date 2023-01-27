@extends('agent._layouts.list')

@section('content')
@component('agent._slots.panel')
<form class="layui-form" lay-filter="" tbody="0">
    <div class="layui-form layui-card-header layuiadmin-card-header-auto">
        <div class="layui-form-item">
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

@component('agent._slots.panel')
<div class="layui-card-body">
    <table class="layui-table">
        <colgroup>
            <col width="60" />
            <col width="120" />
            <col width="120" />
            <col width="160" />
            <col width="350" />
            <col />
            <col width="80" />
            <col width="80" />
            <col width="130" />
        </colgroup>
        <thead>
            <tr>
                <th>序号</th>
                <th>商户名称</th>
                <th>应用名称</th>
                <th>订单号码</th>
                <th>通知URL</th>
                <th>回复消息</th>
                <th>状态</th>
                <th>失败次数</th>
                <th>通知时间</th>
            </tr>
        </thead>
        <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('agent.notify_downs._list')</tbody>
    </table>
</div>
@endcomponent
@endsection