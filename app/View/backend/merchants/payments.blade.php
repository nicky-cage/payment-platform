@extends('backend._layouts.list')

@section('content')
    @component('backend._slots.panel')
        <div class="layui-card-body">
            <table class="layui-table">
                <colgroup>
                    <col width="60"/>
                    <col width="150"/>
                    <col width="120"/>
                    <col width="120"/>
                    <col width="150"/>
                    <col/>
                    <col width="100"/>
                    <col width="100"/>
                    <col width="100"/>
                    <col width="90"/>
                </colgroup>
                <thead>
                <tr>
                    <th>序号</th>
                    <th>渠道名称</th>
                    <th>支付类型</th>
                    <th>支付名称</th>
                    <th>类型代码</th>
                    <th>支付限额</th>
                    <th>渠道费率(%)</th>
                    <th>商户费率(%)</th>
                    <th>费率利差(%)</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($rows as $k => $r)
                    <tr>
                        <td>{{$r->id}}</td>
                        @if ($r->pay_count != 0)
                            <td rowspan="{{$r->pay_count}}">{{$r->channel_id}} / {{$r->channel_name ?? '-'}}</td>
                        @endif
                        <td>{{$paymentTypes[$r->payment_type] ?? '-'}}</td>
                        <td>{{$r->name}}</td>
                        <td>{{$r->code}}</td>
                        <td>
                            @if ($r->amounts == ''){{$r->amount_min}} - {{$r->amount_max}}
                            @else  {{ $r->amounts }}
                            @endif
                        </td>
                        <td class="payment-rate">{{($r->rate)}}</td>
                        <td><input class="layui-input merchant-rate" value="{{$r->merchant_rate}}" origin_val="{{$r->merchant_rate}}"/></td>
                        <td>
                            @if ($r->has_profit) <span style="color: green; font-weight: bold;">{{$r->rate_profit}}</span>
                            @elseif ($r->rate_profit == 0) 0
                            @else <span style="color: red">{{$r->rate_profit}}</span>
                            @endif
                        </td>
                        <td>
                            <button class="layui-btn save-merchant-rate">保存设定</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endcomponent
    <script>
        layui.use(['jquery'], function () {
            let $ = layui.jquery;
            $(document).on("click", ".save-merchant-rate", function () {
                let that = $(this),
                    tr = that.parent().parent(),
                    payment_id = $("td:eq(0)", tr).text(),
                    payment_rate_text = $(".payment-rate:eq(0)", tr).text(),
                    merchant_rate_text = $(".merchant-rate:eq(0)", tr).val(),
                    origin_val = $(".merchant-rate:eq(0)", tr).attr('origin_val');
                if (isNaN(payment_rate_text) || isNaN(merchant_rate_text)) {
                    sp.alert('费率格式有误: ' + merchant_rate_text);
                    $(".merchant-rate:eq(0)", tr).val(origin_val);
                    return;
                }

                let payment_rate = Number(payment_rate_text), merchant_rate = Number(merchant_rate_text);
                if (merchant_rate < payment_rate) {
                    sp.alert('商户费率 ' + merchant_rate + ' 不能小于渠道费率 ' + payment_rate);
                    $(".merchant-rate:eq(0)", tr).val(origin_val);
                    return;
                }

                let data = {
                    "payment_id": payment_id,
                    "merchant_id": "{{$merchantID}}",
                    "rate": merchant_rate,
                };
                sp.post("/merchants/payment_save", data, function (result) {
                    if (result.code != 0) {
                        sp.alert(result.message);
                        return;
                    }
                    sp.alertSuccess('保存商户支付费率成功!', function () {
                        location.reload();
                    })
                });
            });
        });
    </script>
@endsection