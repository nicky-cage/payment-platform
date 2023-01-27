@foreach ($rows as $k => $r)
    <tr @if ($k == 0) total="{{ $total }}" @endif>
        <td>{{ $r->id }}</td>
        <td>{{ $merchants[$r->merchant_id] ?? '-' }}</td>
        <td>{{ $merchantApps[$r->app_id] ?? '-' }}</td>
        <td>{{ $r->channel_name }}</td>
        <td>{{ $r->order_number }}</td>
        <td>{{ $r->trade_number }}</td>
        <td>{{ $r->amount }}</td>
        <td>{{ $r->amount_paid }}</td>
        <td>{{ $statusTypes[$r->state] ?? '-' }}</td>
        <td>{{ $r->created }}</td>
        <td>{{ $r->finished != 0 ? Carbon\Carbon::parse($r->finished + 8 * 3600)->format('Y-m-d H:i:s') : '-' }}</td>
        <td>{{ $r->upstream_confirmed != 0 ? Carbon\Carbon::parse($r->upstream_confirmed + 8 * 3600)->format('Y-m-d H:i:s') : '-' }} </td>
        <td>{{ $r->downstream_confirmed != 0 ? Carbon\Carbon::parse($r->downstream_confirmed + 8 * 3600)->format('Y-m-d H:i:s') : '-' }} </td>
        <td>{{ $r->downstream_notified != 0 ? Carbon\Carbon::parse($r->downstream_notified + 8 * 3600)->format('Y-m-d H:i:s') : '-' }}</td>
        <td>{{ $r->downstream_notify_count }}</td>
    </tr>
@endforeach