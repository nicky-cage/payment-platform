@foreach ($rows as $k => $r)
    <tr @if ($k==0) total="{{ $total }}" @endif>
        <td>{{ $r->id }}</td>
        <td>{{ $r->merchant_name }}</td>
        <td>{{ $merchantApps[$r->app_id] ?? '-' }}</td>
        <td>{{ $r->channel_name }}</td>
        <td>{{ $channelPayments[$r->payment_id] ?? '-' }}</td>
        <td>{{ $r->order_number }}</td>
        <td>{{ $r->amount }}</td>
        <td>
            @if ($r->amount_paid == $r->amount) <span style="color: green">{{ $r->amount_paid }}</span>
            @elseif ($r->amount_paid == 0)
                <del>0.00</del>
            @else {{$r->amount_paid}}
            @endif
        </td>
        <td>{{$r->rate}}%</td>
        <td>
            @if ($r->rate > 0 && $r->platform_rate > 0) {{sprintf("%.2f", ($r->rate - $r->platform_rate) * $r->amount / 100)}}
            @else -
            @endif
        </td>
        <td>
            @if ($r->state == 1) <span style="color: green">成功/已完成</span>
            @elseif ($r->state == 2) <span style="color: red">失败</span>
            @elseif ($r->state == 0) <span style="color: grey">待支付</span>
            @elseif ($r->state == 3) <span style="color: orange">已取消</span>
            @elseif ($r->state == 4)
                <del>已拒绝</del>
            @else 其他
            @endif
            ({{ $r->state }})
        </td>
        <td>{{ $r->created }}</td>
        <td>{{ $r->finished != 0 ? Carbon\Carbon::parse($r->finished + 8 * 3600)->format('Y-m-d H:i:s') : '-' }}</td>
        <td>{{ $r->remark }}</td>
        <td>
            @if ($r->state == 0)
                <button class="layui-btn layui-btn-normal set-order-success" order_id="{{ $r->id }}">成功</button>
                <button class="layui-btn layui-btn-danger set-order-failure" order_id="{{ $r->id }}">失败</button>
            @elseif ($r->state == 2)
                <button class="layui-btn layui-btn-normal set-order-success" order_id="{{ $r->id }}">成功</button>
            @endif
            <button class="layui-btn sp-open-link" url="/{{ $controller }}/update" area="750px,300px">备注</button>
        </td>
    </tr>
@endforeach
