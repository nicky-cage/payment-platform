@extends('agent._layouts.list')

@section('content')
@component('agent._slots.panel')
<form class="layui-form" lay-filter="" tbody="0">
    <div class="layui-form layui-card-header layuiadmin-card-header-auto">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">支付名称</label>
                <div class="layui-input-inline">
                    <input type="text" name="channels_name" placeholder="请输入支付方式名称" autocomplete="off" class="layui-input" />
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">支付编码</label>
                <div class="layui-input-inline">
                    <input type="text" name="channel_id" placeholder="请输入支付方式编码" autocomplete="off" class="layui-input" />
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
    <button class="layui-btn sp-open-link" url="/{{$controller}}/create" area="750px,500px">新增支付方式</button>
</div>
@endcomponent

@component('agent._slots.panel')
<div class="layui-card-body">
    <table class="layui-table">
        <colgroup>
            <col width="100" />
            <col width="150" />
            <col width="180" />
            <col width="80" />
            <col width="100" />
            <col width="100" />
            <col width="350" />
            <col width="130" />
            <col width="130" />
            <col />
        </colgroup>
        <thead>
            <tr>
                <th>* 支式类型 *</th>
                <th>支付分类</th>
                <th>支付名称</th>
                <th>状态</th>
                <th>最小金额</th>
                <th>最大金额</th>
                <th>固定金额</th>
                <th>点位</th>
                <th>修改时间</th>
                <th>备注</th>
            </tr>
        </thead>
        <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('agent.channel_payments._list')</tbody>
    </table>
</div>
@endcomponent
@endsection