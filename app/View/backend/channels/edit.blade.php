@extends('backend._layouts.edit')

@section('content')
    <div class="layui-form-item">
        <label class="layui-form-label form-label">渠道名称</label>
        <div class="layui-input-inline">
            <input type="text" name="name" lay-verify="required" placeholder="请输入渠道名称, 如: 大象支付" autocomplete="off" class="layui-input" value="{{$r->name}}"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label form-label">渠道编码</label>
        <div class="layui-input-inline">
            <input type="text" name="code" lay-verify="required" placeholder="请输入渠道编码, 如: TKY" autocomplete="off" class="layui-input" value="{{$r->code}}"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label form-label">外部编号</label>
        <div class="layui-input-inline">
            <input type="text" name="external_id" lay-verify="required" placeholder="请输入外部商户编号" autocomplete="off" class="layui-input" value="{{$r->external_id ?? 'MERCHANT_ID'. mt_rand(1000, 9999)}}"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label form-label">APP ID</label>
        <div class="layui-input-inline">
            <input type="text" name="app_id" lay-verify="required" placeholder="请输入外部应用/商户编号" autocomplete="off" class="layui-input" value="{{$r->app_id ?? mt_rand(1000, 9999)}}"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label form-label">APP KEY</label>
        <div class="layui-input-inline">
            <input type="text" name="app_key" lay-verify="required" placeholder="请输入APP KEY" autocomplete="off" class="layui-input" value="{{$r->app_key ?? 'KEY'.mt_rand(1000, 9999)}}" style="width: 380px;"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label form-label">APP SECRET</label>
        <div class="layui-input-inline">
            <input type="text" name="app_secret" lay-verify="required" placeholder="请输入APP SECRET" autocomplete="off" class="layui-input" value="{{$r->app_secret ?? 'SECRET'. mt_rand(1000, 9999)}}" style="width: 380px;"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label form-label">下单地址</label>
        <div class="layui-input-inline">
            <input type="text" name="url_order" lay-verify="required" placeholder="请输入下单地址" autocomplete="off" class="layui-input" value="{{$r->url_order ?? mt_rand(1000, 9999) }}" style="width: 380px;"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label form-label">回调地址</label>
        <div class="layui-input-inline">
            <input type="text" name="url_notify" lay-verify="required" placeholder="请输入回调地址" autocomplete="off" class="layui-input" value="{{$r->url_notify ?? 'notify url'}}" style="width: 380px;"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label form-label">备注</label>
        <div class="layui-input-inline">
            <input type="text" name="remark" lay-verify="required" style="width: 380px;" placeholder="请输入备注" autocomplete="off" class="layui-input" value="{{$r->remark ?? '请输入备注'}}"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label label-radio" style="width: 120px;">状态</label>
        <div class="layui-input-inline">
            <input type="radio" name="state" value="0" title="停用" @if($r->state == 0) checked @endif />
            <input type="radio" name="state" value="1" title="启用" @if($r->state == 1) checked @endif />
        </div>
    </div>
@endsection

