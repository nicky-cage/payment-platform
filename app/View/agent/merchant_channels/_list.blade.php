@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td></td>
        <td>{{$r->merchant_id}}</td>
        <td>{{$r->amount}}</td>
        <td>{{$r->remark}}</td>
        <td></td>
    </tr>
@endforeach
