@extends('agent._layouts.list')

@section('content')
@component('agent._slots.panel')
<form class="layui-form" lay-filter="" tbody="0">
    <div class="layui-form layui-card-header layui-form-item layuiadmin-card-header-auto">
        <div class="layui-inline">
            <label class="layui-form-label">用户名称</label>
            <div class="layui-input-inline">
                <input type="text" name="name" placeholder="请输入用户名称" autocomplete="off" class="layui-input" id="name" />
            </div>
        </div>
        <div class="layui-inline">
            <label class="layui-form-label">账号状态</label>
            <div class="layui-input-inline">
                <select name="state" id="state">
                    <option value="">请选择状态</option>
                    <option value="0">禁用</option>
                    <option value="1">正常</option>
                </select>
            </div>
        </div>
        <div class="layui-inline">
            <button class="layui-btn layuiadmin-btn-list" lay-submit lay-filter="sp-form-search">
                <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
            </button>
            {{-- <button class="layui-btn" type="button" id="btnReset">重置</button>--}}
        </div>
    </div>
</form>
<div class="layui-card-body">
    <button class="layui-btn sp-open-link" url="/admins/create" area="600px,600px">新增账号</button>
</div>
@endcomponent

@component('agent._slots.panel')
<div class="layui-card-body">
    <table class="layui-table">
        <colgroup>
            <col width="80" />
            <col width="120" />
            <col width="120" />
            <col width="150" />
            <col />
            <col width="100" />
            <col width="145" />
            <col width="145" />
            <col width="80" />
            <col width="160" />
        </colgroup>
        <thead>
            <tr>
                <th>序号</th>
                <th>账号</th>
                <th>昵称</th>
                <th>邮箱账号</th>
                <th>授权IP</th>
                <th>角色</th>
                <th>创建时间</th>
                <th>最后登录时间</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody id="sp-loaded-table" loaded="loaded" url="/admins">
            @include('agent.admins._list')
        </tbody>
    </table>
</div>
@endcomponent
@endsection