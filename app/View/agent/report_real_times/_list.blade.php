@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->merchant_id}}</td>
        <td>{{$r->year}}</td>
        <td></td>
        <td>{{$r->income}}</td>
        <td>{{$r->cost}}</td>
        <td>{{$r->profit}}</td>
        <td>{{$r->success_total}}/{{$r->success_count}}</td>
        <td>{{$r->failure_total}}/{{$r->failure_count}}</td>
    </tr>
@endforeach
