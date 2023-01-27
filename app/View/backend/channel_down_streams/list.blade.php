@extends('backend._layouts.list')

@section('content')
@component('backend._slots.panel')
<form class="layui-form" lay-filter="" tbody="0">
    <div class="layui-form layui-card-header layuiadmin-card-header-auto">
        <div class="layui-form-item">
            <div class="layui-inline">
                <label class="layui-form-label">渠道名称</label>
                <div class="layui-input-inline">
                    <input type="text" name="up_stream_name" placeholder="请输入渠道名称" autocomplete="off" class="layui-input" />
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
            <col width="200" />
            <col />
            <col width="100" />
            <col width="100" />
            <col width="150" />
            <col width="135" />
            <col width="150" />
        </colgroup>
        <thead>
            <tr>
                <th>序号</th>
                <th>通道编码</th>
                <th>通道名称</th>
                <th>代付类型</th>
                <th>代付费率</th>
                <th>代付最低费率</th>
                <th>添加时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">
            @include('backend.channel_down_streams._list')
        </tbody>
    </table>
</div>
@endcomponent
@endsection