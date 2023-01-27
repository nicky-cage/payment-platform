@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->merchant_id}} / {{$merchants[$r->merchant_id] ?? '-'}}</td>
        <td>{{$r->app_id}} / {{$merchantApps[$r->app_id] ?? '-'}}</td>
        <td>{{$r->order_number}}</td>
        <td>{{$r->notify_url}}</td>
        <td>{{$r->notify_reply}}</td>
        <td>{{$statusTypes[$r->notify_status] ?? '-'}}</td>
        <td>{{$r->failure_count}}</td>
        <td>{{$r->created}}</td>
    </tr>
@endforeach
