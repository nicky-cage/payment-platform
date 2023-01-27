@foreach ($rows as $k => $r)
    <tr @if ($k == 0) total="{{ $total }}" @endif>
        <td>{{ $r->id }}</td>
        <td>{{ $merchants[$r->merchant_id] ?? '-' }}</td>
        <td>{{ $merchantApps[$r->app_id] ?? '-' }}</td>
        <td>{{ $r->channel_name }}</td>
        <td>{{ $r->order_number }}</td>
        <td>{{ $r->amount }}</td>
        <td>{{ $r->amount_paid }}</td>
        <td>{{ $statusTypes[$r->state] ?? '-' }}</td>
        <td>{{ $r->created }}</td>
        <td>{{ $r->finished != 0 ? Carbon\Carbon::parse($r->finished + 8 * 3600)->format('Y-m-d H:i:s') : '-' }}</td>
        <td>{{ $r->remark }}</td>
        <td>
            <button class="layui-btn set-success" order_id="{{$r->id}}" area="750px,360px">完成</button>
            <button class="layui-btn layui-btn-danger set-deny" order_id="{{$r->id}}" area="750px,360px">拒绝</button>
        </td>
    </tr>
@endforeach
