@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->merchant_id}}</td>
        <td>{{$r->merchant_name}}</td>
        <td>{{$r->domain}}</td>
        <td>{{$r->login_ip}}</td>
        <td>{{$r->login_area}}</td>
        <td>{{$r->created}}</td>
    </tr>
@endforeach
