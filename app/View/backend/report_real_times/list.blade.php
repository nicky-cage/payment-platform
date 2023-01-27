@extends('backend._layouts.list')

@section('content')
@component('backend._slots.panel')
<form class="layui-form" lay-filter="" tbody="0">
    <div class="layui-form layui-card-header layuiadmin-card-header-auto">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">商户名称</label>
                <div class="layui-input-inline">
                    <input type="text" name="merchant_name" placeholder="请输入渠道名称" autocomplete="off" class="layui-input" />
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
            <col />
            <col width="100" />
            <col width="100" />
            <col width="90" />
            <col width="90" />
            <col width="90" />
            <col width="150" />
            <col width="150" />
        </colgroup>
        <thead>
            <tr>
                <th>序号</th>
                <th>商户名称</th>
                <th>统计年份</th>
                <th>支付方式</th>
                <th>平台收入</th>
                <th>平台成本</th>
                <th>平台利润</th>
                <th>成功金额/笔数</th>
                <th>失败金额/笔数</th>
            </tr>
        </thead>
        <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('backend.report_real_times._list')</tbody>
    </table>
</div>
@endcomponent
@endsection