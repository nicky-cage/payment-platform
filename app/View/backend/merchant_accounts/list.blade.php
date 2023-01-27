@extends('backend._layouts.list')

@section('content')
    @component('backend._slots.panel')
        <form class="layui-form" lay-filter="" tbody="0">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">商户编号</label>
                        <div class="layui-input-inline">
                            <input type="text" name="merchant_id" placeholder="请输入商户编号" autocomplete="off" class="layui-input"/>
                        </div>
                    </div>
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
    @endcomponent

    @component('backend._slots.panel')
        <div class="layui-card-body">
            <table class="layui-table">
                <colgroup>
                    <col width="60"/>
                    <col width="150"/>
                    <col width="80"/>
                    <col width="80"/>
                    <col width="80"/>
                    <col width="100"/>
                    <col width="80"/>
                    <col width="80"/>
                    <col width="80"/>
                    <col width="80"/>
                    <col width="80"/>
                    <col width="80"/>
                    <col width="80"/>
                    <col width="80"/>
                    <col width="80"/>
                    <col width="80"/>
                    <col width="80"/>
                    <col/>
                </colgroup>
                <thead>
                <tr>
                    <th>序号</th>
                    <th>商户名称</th>
                    <th>应用数量</th>
                    <th>订单总数</th>
                    <th>有效订单</th>
                    <th>成功比率(%)</th>
                    <th>订单总额</th>
                    <th>有效总额</th>
                    <th>提现总额</th>
                    <th>账户总额</th>
                    <th>可用余额</th>
                    <th>冻结金额</th>
                    <th>入账总额</th>
                    <th>出账总额</th>
                    <th>商户实收</th>
                    <th>平台实收</th>
                    <th>利润</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('backend.merchant_accounts._list')</tbody>
            </table>
        </div>
    @endcomponent
@endsection