@extends('backend._layouts.list')

@section('content')
@component('backend._slots.panel')
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
@endcomponent

@component('backend._slots.panel')
<div class="layui-card-body">
    <table class="layui-table">
        <colgroup>
            <col width="60" />
            <col />
            <col width="200" />
            <col width="150" />
            <col width="200" />
            <col width="150" />
            <col width="150" />
        </colgroup>
        <thead>
            <tr>
                <th>序号</th>
                <th>渠道名称</th>
                <th>交易时间</th>
                <th>交易限额</th>
                <th>代付时间</th>
                <th>代付限额</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">
            @include('backend.channel_configs._list')
        </tbody>
    </table>
</div>
@endcomponent
@endsection