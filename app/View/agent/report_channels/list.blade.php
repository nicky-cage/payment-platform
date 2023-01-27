@extends('agent._layouts.list')

@section('content')
@component('agent._slots.panel')
<div style="padding: 10px 15px;">
    <div class="layui-card-header" style="padding-left: 0px;"><strong>大鱼支付 | 总额: 00.00</strong></div>
    <div class="layui-card-body" style="padding: 0px;">
        <table class="layui-table">
            <colgroup>
                <col width="150" />
                <col width="300" />
                <col width="150" />
                <col />
            </colgroup>
            <thead>
                <tr>
                    <th>商户编号</th>
                    <th>商户名称</th>
                    <th>额度</th>
                    <th>备注</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td>0.00</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endcomponent
@endsection