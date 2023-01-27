@extends('backend._layouts.base')

@section('content')
    <div class="layui-row">
        <div class="layui-col-md3">
            <div class="layui-card" id="DX-pay">
                <div class="layui-card-body layui-form" style="margin-bottom: 62px;">
                    <div class="layui-form-item">
                        <label class="layui-form-label">支付方式</label>
                        <div class="layui-input-inline">
                            <select class="lay-select pay_type">
                                <option value="1">支付宝扫码</option>
                                <option value="3">支付宝转卡</option>
                                <option value="4">支付宝群红包</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">订单金额</label>
                        <div class="layui-input-inline">
                            <input name="dx_amount" lay-verify="required" placeholder="订单金额" autocomplete="off" class="layui-input" value="300" />
                        </div>
                    </div>
                    <div class="layui-input-block layui-footer" style="margin-left: 0px; left: 0px;">
                        <label class="layui-form-label"></label>
                        <input type="button" class="lay-btn submit" value="提交订单: 大象支付" form-id="DX-pay" />
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-md3" style="margin-left: 10px;">
            <div class="layui-card" id="ST-pay">
                <div class="layui-card-body layui-form" style="margin-bottom: 62px;">
                    <div class="layui-form-item">
                        <label class="layui-form-label">支付方式</label>
                        <div class="layui-input-inline">
                            <select class="lay-select pay_type" id="st_pay_type">
                                <option value="8012">综合网关</option>
                                <option value="8015">云闪付转卡</option>
                                <option value="8016">支付宝转卡</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">订单金额</label>
                        <div class="layui-input-inline">
                            <input name="st_amount" lay-verify="required" placeholder="订单金额" autocomplete="off" class="layui-input amount" value="300" />
                        </div>
                    </div>
                    <div class="layui-input-block layui-footer" style="margin-left: 0px; left: 0px;">
                        <label class="layui-form-label"></label>
                        <input type="button" class="lay-btn submit" value="提交订单: ST支付" form-id="ST-pay" />
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-md3" style="margin-left: 10px;">
            <div class="layui-card" id="HJ-pay">
                <div class="layui-card-body layui-form" style="margin-bottom: 62px;">
                    <div class="layui-form-item">
                        <label class="layui-form-label">支付方式</label>
                        <div class="layui-input-inline">
                            <select class="lay-select pay_type" id="st_pay_type">
                                <option value="1">支付宝扫码</option>
                                <option value="3">支付宝转卡</option>
                                <option value="4">支付宝群红包</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">订单金额</label>
                        <div class="layui-input-inline">
                            <input name="st_amount" lay-verify="required" placeholder="订单金额" autocomplete="off" class="layui-input amount" value="300" />
                        </div>
                    </div>
                    <div class="layui-input-block layui-footer" style="margin-left: 0px; left: 0px;">
                        <label class="layui-form-label"></label>
                        <input type="button" class="lay-btn submit" value="提交订单: 火炬支付" form-id="HJ-pay" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    layui.use(['form', 'jquery'], function() {
        let $ = layui.jquery;

        $(".submit").off("click").on("click", function() {
            let that = $(this),
                form_id = that.attr("form-id"),
                container = $("#" + form_id),
                channel = form_id.split("-")[0];
            let pay_type = $(".pay_type:first", container).val(),
                amount = $(".amount:first", container).val();
            let data = {
                "channel": channel,
                "amount": amount,
                "type": pay_type,
                "order_number": "order_number_11119999"
            };
            $.post("/deposit_thirds/pay", data, function(result) {
                console.log(result);
            });
        });
    });
    </script>
@endsection
