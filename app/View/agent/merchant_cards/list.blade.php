@extends('agent._layouts.list')

@section('content')
@component('agent._slots.panel')
<form class="layui-form" lay-filter="" tbody="0">
    <div class="layui-form layui-card-header layuiadmin-card-header-auto">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">银行卡号</label>
                <div class="layui-input-inline">
                    <input type="text" name="card_number" placeholder="请输入银行卡号" autocomplete="off" class="layui-input" />
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
            <col width="150" />
            <col width="250" />
            <col width="150" />
            <col width="250" />
            <col />
            <col width="80" />
            <col width="80" />
            <col width="100" />
        </colgroup>
        <thead>
            <tr>
                <th>序号</th>
                <th>收款名称</th>
                <th>银行卡号</th>
                <th>收款姓名</th>
                <th>支行名称</th>
                <th>备注</th>
                <th>状态</th>
                <th>轮询状态</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('agent.merchant_cards._list')</tbody>
    </table>
</div>
@endcomponent
@endsection