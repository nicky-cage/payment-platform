@extends('backend._layouts.list')

@section('content')
@component('backend._slots.panel')
<form class="layui-form" lay-filter="" tbody="0">
    <div class="layui-form layui-card-header layuiadmin-card-header-auto">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">URL</label>
                <div class="layui-input-inline">
                    <input type="text" name="url" placeholder="请输入URL" autocomplete="off" class="layui-input" />
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">IP</label>
                <div class="layui-input-inline">
                    <input type="text" name="ip" placeholder="请输入IP" autocomplete="off" class="layui-input" />
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
            <col width="250" />
            <col width="250" />
            <col width="100" />
            <col width="130" />
            <col />
            <col width="80" />
        </colgroup>
        <thead>
            <tr>
                <th>序号</th>
                <th>提交路径</th>
                <th>来源IP</th>
                <th>请求数据</th>
                <th>请求时间</th>
                <th>处理结果/备注</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('backend.error_logs._list')</tbody>
    </table>
</div>
@endcomponent
@endsection
