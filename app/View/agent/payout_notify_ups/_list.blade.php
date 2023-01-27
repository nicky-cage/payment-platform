@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->channel_id}}</td>
        <td>{{$r->trade_number}}</td>
        <td>{{$r->request_url}}</td>
        <td>{{$r->request_ip}}</td>
        <td class="sp-alert" style="cursor: pointer;" text='{{$r->request_data}}'>{{\Hyperf\Utils\Str::limit($r->request_data ?? '', 40, '...')}}</td>
        <td>{{$r->reply}}</td>
        <td>{{$r->remark}}</td>
        <td>{{$r->created}}</td>
    </tr>
@endforeach
