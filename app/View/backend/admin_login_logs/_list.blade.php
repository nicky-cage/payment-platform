@foreach ($rows as $k => $r)
<tr @if ($k==0)total="{{$total}}" @endif>
    <td>{{$r->id}}</td>
    <td>{{$r->admin_name}}</td>
    <td>{{$r->domain}}</td>
    <td>{{$r->login_ip}}</td>
    <td>{{$r->login_area}}</td>
    <td>{{$r->created}}</td>
    <td>{{$r->user_agent}}</td>
</tr>
@endforeach