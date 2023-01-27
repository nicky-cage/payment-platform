@extends('backend._layouts.edit')

@section('content')
    <div class="layui-form-item">
        <label class="layui-form-label">商户名称</label>
        <div class="layui-input-inline">
            @if (isset($merchants[$r->merchant_id])){{$merchants[$r->merchant_id]}}@endif
            @if (!isset($merchants[$r->merchant_id]))
                <select name="merchant_id" lay-verify="required" lay-search="">
                    @foreach ($merchants as $k=>$v)
                        <option value="{{$k}}">{{$v}}</option>
                    @endforeach
                </select>
            @endif
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">应用名称</label>
        <div class="layui-input-inline">
            <input type="text" name="name" lay-verify="required" placeholder="请输入应用名称" autocomplete="off" class="layui-input"
                   value="@if ($r->id == 0){{'商户应用00'}}{{mt_rand(10, 99)}}@else{{$r->name}}@endif"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">回调地址</label>
        <div class="layui-input-inline">
            <input type="text" name="notify_url" lay-verify="required" placeholder="请输入应用回调地址" autocomplete="off" class="layui-input"
                   value="@if ($r->id == 0){{'使用订单提交回调地址'}}@else{{$r->notify_url}}@endif" style="width: 380px;"/>
        </div>
    </div>
    @if ($r->id == 0)
        <div class="layui-form-item">
            <label class="layui-form-label label-radio">状态</label>
            <div class="layui-input-inline">
                <input type="radio" name="state" value="1" title="启用" @if ($r->id == 0 || $r->state == 1) checked @endif />
                <input type="radio" name="state" value="0" title="停用" @if ($r->state == 0 && $r->id != 0) checked @endif />
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label label-radio">入款权限</label>
            <div class="layui-input-inline">
                <input type="radio" name="pay_in" value="1" title="启用" @if ($r->id == 0 || $r->pay_in == 1) checked @endif />
                <input type="radio" name="pay_in" value="0" title="停用" @if ($r->id != 0 && $r->pay_in == 0) checked @endif />
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label label-radio">出款权限</label>
            <div class="layui-input-inline">
                <input type="radio" name="pay_out" value="1" title="启用" @if ($r->id == 0 || $r->pay_out == 1) checked @endif />
                <input type="radio" name="pay_out" value="0" title="停用" @if ($r->id != 0 && $r->pay_out == 0) checked @endif />
            </div>
        </div>
    @endif
    <div class="layui-form-item">
        <label class="layui-form-label">授权IP</label>
        <div class="layui-input-inline">
            <textarea class="layui-textarea" lay-verify="require" placeholder="请输入授权IP" name="allow_ips"
                      style="width: 380px;">@if ($r->id == 0){{'127.0.0.1'}}@else{{$r->allow_ips}}@endif</textarea>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">备注</label>
        <div class="layui-input-inline" style="width: 380px">
            <input type="text" name="remark" placeholder="请输入备注" autocomplete="off" class="layui-input"
                   value="@if ($r->id == 0){{'请输入应用备注'}}@else{{$r->remark}}@endif"/>
        </div>
    </div>
@endsection
