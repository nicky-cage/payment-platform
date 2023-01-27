@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->merchant_id}}</td>
        <td>{{$r->merchant_name}}</td>
        <td>{{$changeTypes[$r->change_type]}}</td>
        <td>{{$r->amount}}</td>
        <td>{{$r->remain_before}}</td>
        <td>{{$r->remain_after}}</td>
        <td>{{$r->remark}}</td>
        <td>{{$r->created}}</td>
    </tr>
@endforeach
