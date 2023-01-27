@extends('agent._layouts.list')

@section('content')
@component('agent._slots.panel')
<form class="layui-form" lay-filter="" tbody="0">
    <div class="layui-form layui-card-header layuiadmin-card-header-auto">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">渠道名称</label>
                <div class="layui-input-inline">
                    <input type="text" name="name" placeholder="请输入渠道名称" autocomplete="off" class="layui-input" />
                </div>
            </div>
        </div>
    </div>
</form>
<div class="layui-card-body">
    <button class="layui-btn sp-open-link" url="/{{$controller}}/create" area="750px,580px">新增渠道</button>
</div>
@endcomponent

@component('agent._slots.panel')
<div class="layui-card-body">
    <table class="layui-table">
        <colgroup>
            <col width="60" />
            <col width="150" />
            <col width="100" />
            <col width="100" />
            <col width="200" />
            <col width="100" />
            <col width="135" />
            <col />
            <col width="60" />
        </colgroup>
        <thead>
            <tr>
                <th>序号</th>
                <th>渠道名称</th>
                <th>渠道编码</th>
                <th>优先序号</th>
                <th>回调IP</th>
                <th>状态</th>
                <th>创建时间</th>
                <th>备注</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('agent.channel_up_streams._list')</tbody>
    </table>
</div>
@endcomponent
@endsection