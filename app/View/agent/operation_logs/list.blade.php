@extends('agent._layouts.list')

@section('content')
@component('agent._slots.panel')
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

@component('agent._slots.panel')
<div class="layui-card-body">
    <table class="layui-table">
        <colgroup>
            <col width="60" />
            <col width="120" />
            <col width="150" />
            <col />
            <col width="90" />
            <col width="200" />
            <col width="150" />
            <col width="150" />
            <col width="135" />
        </colgroup>
        <thead>
            <tr>
                <th>序号</th>
                <th>用户编号</th>
                <th>用户名称</th>
                <th>备注</th>
                <th>方法</th>
                <th>来源URL</th>
                <th>操作IP</th>
                <th>操作地区</th>
                <th>操作时间</th>
            </tr>
        </thead>
        <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('agent.operation_logs._list')</tbody>
    </table>
</div>
@endcomponent
@endsection