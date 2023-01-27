@foreach ($rows as $k => $r)
<tr @if ($k==0) total="{{ $total }}" @endif>
    <td>{{ $r->id }}</td>
    <td>{{ $merchants[$r->merchant_id] ?? '-' }}</td>
    <td>{{ $merchantApps[$r->app_id] ?? '-' }}</td>
    <td>{{ $r->channel_name }}</td>
    <td>{{ $r->order_number }}</td>
    <td>{{ $r->trade_number }}</td>
    <td>{{ $r->amount }}</td>
    <td>
        @if ($r->amount_paid == $r->amount) <span style="color: green">{{ $r->amount_paid }}</span>
        @elseif ($r->amount_paid == 0)
        <del>0.00</del>
        @else {{ $r->amount_paid }}
        @endif
    </td>
    <td>
        @if ($r->state == 1) <span style="color: green">成功/已完成</span>
        @elseif ($r->state == 2) <span style="color: red">失败</span>
        @elseif ($r->state == 0) <span style="color: grey">待支付</span>
        @elseif ($r->state == 3) <span style="color: orange">已取消</span>
        @elseif ($r->state == 4) <del>已拒绝</del>
        @else 其他
        @endif
        ({{ $r->state }})
    </td>
    <td>{{ $r->created }}</td>
    <td>{{ $r->upstream_confirmed != 0 ? Carbon\Carbon::parse($r->upstream_confirmed + 8 * 3600)->format('Y-m-d H:i:s') : '-' }} </td>
    <td>{{ $r->downstream_confirmed != 0 ? Carbon\Carbon::parse($r->downstream_confirmed + 8 * 3600)->format('Y-m-d H:i:s') : '-' }} </td>
    <td></td>
    <td>
        <button class="layui-btn layui-btn-normal notify-send" order_id="{{ $r->id }}">回调</button>
        <button class="layui-btn query-order" order_id="{{ $r->id }}">查单</button>
        @if ($r->state != 1 && $r->state != 2)
        <button class="layui-btn layui-btn-danger fix-order" order_id="{{ $r->id }}">补单</button>
        @endif 
    </td>
</tr>
@endforeach

