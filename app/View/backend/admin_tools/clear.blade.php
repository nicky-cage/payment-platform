@extends('backend._layouts.edit')

@section('content')
    <div class="layui-form-item">
        <label class="layui-form-label" style="width: 100px;">&nbsp;</label>
        <div class="layui-input-inline">
            <input type="checkbox" checked="checked" title="用户数据" value="user" />
            <input type="checkbox" checked="checked" title="商户数据" value="merchant" />
            <input type="checkbox" checked="checked" title="日志数据" value="log" />
            <input type="checkbox" checked="checked" title="订单数据" value="orders" />
        </div>
    </div>
@endsection
