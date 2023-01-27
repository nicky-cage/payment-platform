@foreach ($rows as $k => $r)
    <tr @if ($k == 0) total="{{ $total }}" @endif>
        <td>{{ $r->id }}</td>
        <td>{{ $merchants[$r->merchant_id] ?? '-' }}</td>
        <td>{{ $merchantApps[$r->app_id] ?? '-' }}</td>
        <td>{{ $r->order_number }}</td>
        <td>{{ $r->trade_number }}</td>
        <td>{{ $r->amount }}</td>
        <td>
            @if ($r->state == 2)<del>{{ $r->amount_paid }}</del>
            @else {{ $r->amount_paid }}
            @endif
        </td>
        <td>
            {{ $statusTypes[$r->state] ?? '-' }}
        </td>
        <td>{{ $r->created }}</td>
        <td>{{ $r->finished != 0 ? Carbon\Carbon::parse($r->finished + 8 * 3600)->format('Y-m-d H:i:s') : '-' }}</td>
        <td>{{ $r->remark }}</td>
    </tr>
@endforeach
