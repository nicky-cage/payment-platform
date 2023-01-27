@extends('backend._layouts.list')

@section('content')
@component('backend._slots.panel')
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

@component('backend._slots.panel')
<div class="layui-card-body">
    <table class="layui-table">
        <colgroup>
            <col width="60" />
            <col width="80" />
            <col width="200" />
            <col width="100" />
            <col />
            <col width="80" />
            <col width="80" />
            <col width="100" />
        </colgroup>
        <thead>
            <tr>
                <th>序号</th>
                <th>商户编号</th>
                <th>商户名称</th>
                <th>银行卡编号</th>
                <th>银行卡号码</th>
                <th>状态</th>
                <th>轮询状态</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('backend.merchant_cards._list')</tbody>
    </table>
</div>
@endcomponent
@endsection