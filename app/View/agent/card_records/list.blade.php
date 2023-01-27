@extends('agent._layouts.list')

@section('content')
    @component('agent._slots.panel')
        <form class="layui-form" lay-filter="" tbody="0">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">商户名称</label>
                        <div class="layui-input-inline">
                            <input type="text" name="name" placeholder="请输入商户名称" autocomplete="off" class="layui-input"/>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">订单号码</label>
                        <div class="layui-input-inline">
                            <input type="text" name="order_number" placeholder="请输入订单号码" autocomplete="off" class="layui-input"/>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">银行订单</label>
                        <div class="layui-input-inline">
                            <input type="text" name="bank_order_number" placeholder="请输入银行卡订单号码" autocomplete="off" class="layui-input"/>
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
                    <col width="60"/>
                    <col width="100"/>
                    <col width="150"/>
                    <col width="100"/>
                    <col width="100"/>
                    <col width="150"/>
                    <col/>
                    <col width="100"/>
                    <col width="150"/>
                    <col width="100"/>
                    <col width="80"/>
                    <col width="135"/>
                    <col width="100"/>
                </colgroup>
                <thead>
                <tr>
                    <th>序号</th>
                    <th>商户名称</th>
                    <th>订单号码</th>
                    <th>银行名称</th>
                    <th>收款姓名</th>
                    <th>支行名称</th>
                    <th>银行卡号</th>
                    <th>付款姓名</th>
                    <th>银行订单号码</th>
                    <th>实付金额</th>
                    <th>状态</th>
                    <th>付款完成时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('agent.card_records._list')</tbody>
            </table>
        </div>
    @endcomponent
@endsection