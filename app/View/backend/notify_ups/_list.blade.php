@foreach ($rows as $k => $r)
<tr @if ($k==0)total="{{$total}}" @endif>
    <td>{{$r->id}}</td>
    <td>{{$r->channel_id}}/{{$r->channel_name}}</td>
    <td>{{$r->app_id}}/{{$r->name}}</td>
    <td>{{$r->trade_number}}</td>
    <td>{{$r->request_url}}</td>
    <td>{{$r->request_ip}}</td>
    <td>
        @if ($r->request_data)
        <a href="javascript:void()" class="show-text" text="<pre>{{print_r(json_decode($r->request_data), true)}}</pre>">查看</a>
        @endif
    </td>
    <td>{{$r->reply}}</td>
    <td>{{$r->remark}}</td>
    <td>{{$r->created}}</td>
</tr>
@endforeach