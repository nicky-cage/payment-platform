@extends('agent._layouts.list')

@section('content')
@component('agent._slots.panel')
<form class="layui-form" lay-filter="" tbody="0">
    <div class="layui-form layui-card-header layuiadmin-card-header-auto">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">银行名称</label>
                <div class="layui-input-inline">
                    <input type="text" name="bank_name" placeholder="请输入银行名称" autocomplete="off" class="layui-input" />
                </div>
            </div>
            <div class="layui-inline">
                <label class="layui-form-label">银行编码</label>
                <div class="layui-input-inline">
                    <input type="text" name="bank_code" placeholder="请输入银行编码" autocomplete="off" class="layui-input" />
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
    <button class="layui-btn sp-open-link" url="/{{$controller}}/create" area="750px,500px">新增银行</button>
</div>
@endcomponent

@component('agent._slots.panel')
<div class="layui-card-body">
    <table class="layui-table">
        <colgroup>
            <col width="60" />
            <col width="350" />
            <col width="100" />
            <col width="350" />
            <col width="80" />
            <col width="100" />
            <col />
            <col width="80" />
        </colgroup>
        <thead>
            <tr>
                <th>序号</th>
                <th>银行名称</th>
                <th>银行编码</th>
                <th>图片</th>
                <th>排序</th>
                <th>状态</th>
                <th>备注</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('agent.banks._list')</tbody>
    </table>
</div>
@endcomponent
@endsection