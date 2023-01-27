@extends('agent._layouts.edit')

@section('content')
    <div class="layui-form-item">
        <label class="layui-form-label">支付渠道</label>
        <div class="layui-input-inline">
            <select name="channel_id" id="channel_id" lay-verify="required">
                <option value="">请选择支付渠道</option>
                @foreach ($channels as $k => $v)
                    <option value="{{$k}}" @if($k==$r->channel_id)selected="selected"@endif>{{$v->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">所有商户/代理</label>
        <div class="layui-input-inline">
            <input type="radio" name='xx' title="是" lay-filter="agent" lay-skin="primary" value="1" checked>
            <input type="radio" name='xx' title="否" lay-filter="agent" lay-skin="primary" value="2">
        </div>
    </div>
    <div class="layui-form-item agent" style="display:none">
        <label class="layui-form-label">选择商户/代理</label>
        <div class="layui-input-inline">
            <select name="merchant" lay-filter="testId">
                <option value="">选择指定代理不同点位</option>
                @foreach ($merchant as $k => $v)
                    <option value="{{$v->id}}">{{$v->name}}</option>
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
                    <option value="{{$k}}" @if($k==$r->payment_type)selected="selected"@endif>{{$v}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">支付方式名称</label>
        <div class="layui-input-inline" style="width: 300px;">
            <input type="text" name="name" lay-verify="required" placeholder="请输入三方支付方式名称, 如: 银行转卡" autocomplete="off" class="layui-input" value="{{$r->name}}"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">支付方式编码</label>
        <div class="layui-input-inline" style="width: 300px;">
            <input type="text" name="code" id="code" lay-verify="required" placeholder="请输入第三方支付方式编码, 如: 9001" autocomplete="off" class="layui-input" value="{{$r->code}}"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">最小金额</label>
        <div class="layui-input-inline" style="width: 75px;">
            <input type="text" name="amount_min" lay-verify="required" placeholder="请输入最小金额" autocomplete="off" class="layui-input" value="{{$r->amount_min ?? 10}}"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">最大金额</label>
        <div class="layui-input-inline" style="width: 75px;">
            <input type="text" name="amount_max" lay-verify="required" placeholder="请输入最大金额" autocomplete="off" class="layui-input" value="{{$r->amount_max ?? 2000}}"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">指定金额</label>
        <div class="layui-input-inline" style="width: 300px;">
            <input type="text" name="amounts" placeholder="如有指定金额,请输入三方支付金额, 如: 30,50" autocomplete="off" class="layui-input" value="{{$r->amounts}}"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">上游点位</label>
        <div class="layui-input-inline" style="width: 300px;">
            <input type="text" autocomplete="off" class="layui-input" disabled value="{{$r->rate}}"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">下游点位</label>
        <div class="layui-input-inline" style="width: 300px;">
            <input type="text" name="rate_lower" id="rate_lower" placeholder="请输入点位" autocomplete="off" class="layui-input" value="{{$r->rate_lower}}"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">状态</label>
        <div class="layui-input-inline">
            <select name="state" lay-verify="required">
                <option value="0">禁用</option>
                <option value="1" @if($r->state == 1) selected="selected" @endif>启用</option>
            </select>
        </div>
    </div>
    <script>
        layui.use(['jquery', 'layer', 'tree', 'form'], function () {
            let $ = layui.jquery;

            layui.form.on('radio(agent)', function (data) {
                if (data.value == 2) {
                    $(".agent").css({"display": "block"})
                } else {
                    $(".agent").css("display", "none")
                }
            })
            layui.form.on('select(testId)', function (data) {
                let code = $("#code").val();
                let channelId = $("#channel_id").val();
                let id = data.value;
                if (data.value != "") {
                    $.ajax({
                        url: "/channel_payments/get_info?id=" + id + "&code=" + code + "&channel_id=" + channelId,
                        type: "get",
                        success: function (data) {
                            $("input[name='rate_lower']").val(data.message);
                        }
                    })
                }
            })
        })
    </script>
@endsection