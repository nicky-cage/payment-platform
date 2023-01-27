@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->merchant_id}}</td>
        <td>{{$r->merchant_name}}</td>
        <td>{{$r->remain}}</td>
        <td>{{$r->frozen}}</td>
        <td>{{$r->total_in}}</td>
        <td>{{$r->total_out}}</td>
        <td>{{$r->total}}</td>
        <td>{{$statusTypes[$r->state] ?? '-'}}</td>
        <td>{{$r->remark}}</td>
        <td></td>
    </tr>
@endforeach
