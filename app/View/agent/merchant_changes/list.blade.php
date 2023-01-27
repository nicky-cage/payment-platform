@extends('agent._layouts.list')

@section('content')
@component('agent._slots.panel')
<form class="layui-form" lay-filter="" tbody="0">
    <div class="layui-form layui-card-header layuiadmin-card-header-auto">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">账变类型</label>
                <div class="layui-input-inline">
                    <input type="text" name="merchant_id" placeholder="请输入商户编号" autocomplete="off" class="layui-input" />
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
            <col width="100" />
            <col width="120" />
            <col width="120" />
            <col width="130" />
            <col width="100" />
            <col />
        </colgroup>
        <thead>
            <tr>
                <th>序号</th>
                <th>商户名称</th>
                <th>账变类型</th>
                <th>账变金额</th>
                <th>账变前余额</th>
                <th>账变后余额</th>
                <th>账变时间</th>
                <th>状态</th>
                <th>备注</th>
            </tr>
        </thead>
        <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('agent.merchant_changes._list')</tbody>
    </table>
</div>
@endcomponent
@endsection