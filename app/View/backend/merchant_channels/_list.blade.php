@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->merchant_id}}</td>
        <td>{{$r->merchant_name}}</td>
        <td>{{$r->amount}}</td>
        <td>{{$r->remark}}</td>
    </tr>
@endforeach
