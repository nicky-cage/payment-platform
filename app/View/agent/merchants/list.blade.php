@extends('agent._layouts.list')

@section('content')
    @component('agent._slots.panel')
        <form class="layui-form" lay-filter="" tbody="0">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">商户名称</label>
                        <div class="layui-input-inline">
                            <input type="text" name="merchant_name" placeholder="请输入商户名称" autocomplete="off" class="layui-input"/>
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
            <button class="layui-btn sp-open-link" url="/{{$controller}}/create" area="750px,650px">新增商户</button>
        </div>
    @endcomponent

    @component('agent._slots.panel')
        <div class="layui-card-body">
            <table class="layui-table">
                <colgroup>
                    <col width="60"/>
                    <col width="180"/>
                    <col width="150"/>
                    <col width="150"/>
                    <col/>
                    <col width="100"/>
                    <col width="80"/>
                    <col width="80"/>
                    <col width="60"/>
                    <col width="60"/>
                    <col width="130"/>
                </colgroup>
                <thead>
                <tr>
                    <th>序号</th>
                    <th>商户编号</th>
                    <th>商户名称</th>
                    <th>上级商户</th>
                    <th>手机号码</th>
                    <th>电子邮件</th>
                    <!-- <th>账户余额</th>
                     <th>账户冻结余额</th>-->
                    <th>入款权限</th>
                    <th>出款权限</th>
                    <th>状态</th>
                    <th>排序</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('agent.merchants._list')</tbody>
            </table>
        </div>
    @endcomponent
@endsection