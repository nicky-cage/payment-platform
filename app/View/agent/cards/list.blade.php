@extends('agent._layouts.list')

@section('content')
    @component('agent._slots.panel')
        <form class="layui-form" lay-filter="" tbody="0">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">银行名称</label>
                        <div class="layui-input-inline">
                            <input type="text" name="bank_name" placeholder="请输入银行名称" autocomplete="off" class="layui-input"/>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">银行编码</label>
                        <div class="layui-input-inline">
                            <input type="text" name="bank_code" placeholder="请输入银行编码" autocomplete="off" class="layui-input"/>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">银行卡号</label>
                        <div class="layui-input-inline">
                            <input type="text" name="card_number" placeholder="请输入银行卡号" autocomplete="off" class="layui-input"/>
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
                    <col width="120"/>
                    <col width="120"/>
                    <col width="150"/>
                    <col/>
                    <col width="100"/>
                    <col width="90"/>
                    <col width="90"/>
                    <col width="90"/>
                    <col width="90"/>
                    <col width="90"/>
                    <col width="135"/>
                    <col width="135"/>
                    <col width="80"/>
                </colgroup>
                <thead>
                <tr>
                    <th>序号</th>
                    <th>银行名称</th>
                    <th>银行编码</th>
                    <th>支行信息</th>
                    <th>银行卡号</th>
                    <th>持卡姓名</th>
                    <th>每笔最低</th>
                    <th>每笔最高</th>
                    <th>最高支付</th>
                    <th>最多调用</th>
                    <th>总额度</th>
                    <th>添加时间</th>
                    <th>最后修改</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('agent.cards._list')</tbody>
            </table>
        </div>
    @endcomponent
@endsection