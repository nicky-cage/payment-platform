@extends('backend._layouts.list')

@section('content')
@component('backend._slots.panel')
<form class="layui-form" lay-filter="" tbody="0">
    <div class="layui-form layui-card-header layuiadmin-card-header-auto">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">用户名称</label>
                <div class="layui-input-inline">
                    <input type="text" name="name" placeholder="请输入用户名称" autocomplete="off" class="layui-input" />
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
            <col width="100" />
            <col width="120" />
            <col />
            <col width="150" />
            <col width="100" />
            <col width="135" />
        </colgroup>
        <thead>
            <tr>
                <th>序号</th>
                <th>渠道编号/名称</th>
                <th>上游单号</th>
                <th>来源URL</th>
                <th>来源IP</th>
                <th>接收信息</th>
                <th>回复消息</th>
                <th>备注</th>
                <th>操作时间</th>
            </tr>
        </thead>
        <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('backend.payout_notify_ups._list')</tbody>
    </table>
</div>
@endcomponent
@endsection