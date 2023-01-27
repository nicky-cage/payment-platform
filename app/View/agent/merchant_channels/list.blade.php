@extends('agent._layouts.list')

@section('content')
@component('agent._slots.panel')
<div style="padding: 10px 15px;">
    <div class="layui-card-body" style="padding: 0px;">
        <table class="layui-table">
            <colgroup>
                <col width="150" />
                <col width="150" />
                <col width="150" />
                <col width="150" />
                <col />
                <col width="100" />
            </colgroup>
            <thead>
                <tr>
                    <th>商户名称</th>
                    <th>账户余额</th>
                    <th>最后刷新时间</th>
                    <th>备注</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('agent.merchant_channels._list')</tbody>
        </table>
    </div>
</div>
@endcomponent
@endsection