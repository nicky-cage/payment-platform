@foreach ($rows as $k => $r)
    <tr @if ($k == 0) total="{{ $total }}" @endif>
        <td>{{ $r->id }}</td>
        <td>{{ $merchants[$r->merchant_id] ?? '-' }}</td>
        <td>{{ $merchantApps[$r->app_id] ?? '-' }}</td>
        <td>{{ $r->order_number }}</td>
        <td>{{ $r->amount }}</td>
        <td>{{ $r->amount_paid }}</td>
        <td>
            {{ $statusTypes[$r->state] ?? '-' }}
        </td>
        <td>{{ $r->created }}</td>
        <td>{{ $r->finished != 0 ? Carbon\Carbon::parse($r->finished + 8 * 3600)->format('Y-m-d H:i:s') : '-' }}</td>
        <td>{{ $r->remark }}</td>
        <td> <button class="layui-btn layui-btn-danger cancel-payout" url="/{{ $controller }}/cancel" order_id="{{$r->id}}"  area="750px,360px">取消</button> </td>
    </tr>
@endforeach
