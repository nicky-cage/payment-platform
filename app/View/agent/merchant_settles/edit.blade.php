@extends('agent._layouts.edit')

@section('content')
<div class="layui-form-item">
    <label class="layui-form-label">商户名称</label>
    <div class="layui-input-inline">
        <select name="merchant_id" lay-verify="required" lay-search="">
            @foreach ($merchant as $k=>$v)
            <option value="{{$k}}" @if($k==$r->merchant_id)selected="selected"@endif>{{$v}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">结算金额</label>
    <div class="layui-input-inline">
        <input type="text" name="amount" lay-verify="required" placeholder="请输入应用名称" autocomplete="off" class="layui-input" value="{{$r->amount}}" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">手续费</label>
    <div class="layui-input-inline">
        <input type="text" name="fee" lay-verify="required" placeholder="请输入应用名称" autocomplete="off" class="layui-input" value="{{$r->fee}}" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">实际结算金额</label>
    <div class="layui-input-inline">
        <input type="text" name="amount_settled" lay-verify="required" placeholder="请输入应用名称" autocomplete="off" class="layui-input" value="{{$r->amount_settled}}" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">银行名称</label>
    <div class="layui-input-inline">
        <select name="bank_id" lay-verify="required" lay-search="">
            @foreach ($banks as $k=>$v)
            <option value="{{$k}}" @if($k==$r->bank_id)selected="selected"@endif>{{$v}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">支行名称</label>
    <div class="layui-input-inline">
        <input type="text" name="branch_name" lay-verify="required" placeholder="请输入应用名称" autocomplete="off" class="layui-input" value="{{$r->branch_name}}" />
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">姓名</label>
    <div class="layui-input-inline">
        <input type="text" name="real_name" lay-verify="required" placeholder="请输入应用名称" autocomplete="off" class="layui-input" value="{{$r->real_name}}" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">银行卡号</label>
    <div class="layui-input-inline">
        <input type="text" name="card_number" lay-verify="required" placeholder="请输入应用名称" autocomplete="off" class="layui-input" value="{{$r->card_number}}" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">手机号</label>
    <div class="layui-input-inline">
        <input type="text" name="phone" lay-verify="required" placeholder="请输入应用名称" autocomplete="off" class="layui-input" value="{{$r->phone}}" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">联动选择框</label>
    <div class="layui-input-inline province">
        <select name="province_id" lay-verify="required" lay-filter="province" id="province_id">
            <option value="" data-data="">请选择省份</option>
            @foreach ($province as $k=>$v)
            <option value="{{$k}}" @if($k==$r->province_id)selected="selected"@endif data-data="{{$v['code']}}">{{$v['name']}}</option>
            @endforeach
        </select>
    </div>
    <div class="layui-input-inline city">
        <select name="city_id" lay-verify="required" lay-filter="city" id="city_id">
            @foreach ($city as $k=>$v)
            <option value="{{$k}}" @if($k==$r->city_id)selected="selected"@endif data-data="{{$v['code']}}">{{$v['name']}}</option>
            @endforeach
        </select>
    </div>
    <div class="layui-input-inline district">
        <select name="district_id" lay-verify="required" lay-filter="district" id="district_id">
            @foreach ($district as $k=>$v)
            <option value="{{$k}}" @if($k==$r->district_id)selected="selected"@endif data-data="{{$v['code']}}">{{$v['name']}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">状态</label>
    <div class="layui-input-block">
        <input type="radio" name="state" value="0" title="待付" @if ($r->state == 0) checked @endif />
        <input type="radio" name="state" value="1" title="完成" @if ($r->state == 1) checked @endif />
        <input type="radio" name="state" value="2" title="取消" @if ($r->state == 2) checked @endif />
        <input type="radio" name="state" value="3" title="拒绝" @if ($r->state == 3) checked @endif />
        <input type="radio" name="state" value="9" title="其他" @if ($r->state == 9) checked @endif />
    </div>
</div>
@endsection