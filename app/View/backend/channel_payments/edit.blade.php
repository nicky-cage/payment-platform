@extends('backend._layouts.edit')

@section('content')
    <div class="layui-form-item">
        <label class="layui-form-label">支付渠道</label>
        <div class="layui-input-inline">
            <select name="channel_id" lay-verify="required">
                <option value="">请选择支付渠道</option>
                @foreach ($channels as $k => $v)
                    <option value="{{$k}}" @if($k == $r->channel_id)selected="selected"@endif>{{$v->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">支付类型</label>
        <div class="layui-input-inline">
            <select name="payment_type" lay-verify="required">
                <option value="">请选择支付类型</option>
                @foreach (\App\Constants\Consts::PaymentTypes as $k => $v)
                    <option value="{{$k}}" @if($k == $r->payment_type)selected="selected"@endif>{{$v}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">支付方式名称</label>
        <div class="layui-input-inline" style="width: 190px;">
            <input name="name" lay-verify="required" placeholder="输入支付方式名称, 如: 银行转卡"
                   autocomplete="off" class="layui-input" value="{{$r->name}}"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">支付方式编码</label>
        <div class="layui-input-inline" style="width: 190px;">
            <input name="code" lay-verify="required" placeholder="输入支付方式编码, 如: 9001"
                   autocomplete="off" class="layui-input" value="{{$r->code}}"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">最小金额</label>
        <div class="layui-input-inline" style="width: 95px;">
            <input name="amount_min" lay-verify="required" placeholder="请输入最小金额" autocomplete="off" class="layui-input" value="{{$r->amount_min ?? 10}}"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">最大金额</label>
        <div class="layui-input-inline" style="width: 95px;">
            <input name="amount_max" lay-verify="required" placeholder="请输入最大金额" autocomplete="off" class="layui-input" value="{{$r->amount_max ?? 2000}}"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">指定金额</label>
        <div class="layui-input-inline" style="width: 380px;">
            <input name="amounts" placeholder="如有指定金额,请输入三方支付金额, 如: 30,50" autocomplete="off" class="layui-input" value="{{$r->amounts}}"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">上游点位(%)</label>
        <div class="layui-input-inline" style="width: 95px;">
            <input name="rate" placeholder="输入渠道费率" autocomplete="off" class="layui-input" value="{{$r->rate}}"/>
        </div>
    </div>
    @if ($r->id == 0)
        <div class="layui-form-item">
            <label class="layui-form-label">状态</label>
            <div class="layui-input-inline">
                <select name="state" lay-verify="required">
                    <option value="1" selected="selected">启用</option>
                    <option value="0">禁用</option>
                </select>
            </div>
        </div>
    @endif
@endsection
