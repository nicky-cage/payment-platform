@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->day}}</td>
        <td>{{$r->pay_money}}</td>
        <td>{{$r->pay_num}}</td>
        <td>0</td>
        <td>{{$r->withdraw_money}}</td>
        <td>{{$r->withdraw_num}}</td>
        <td>0</td>
        <td>{{$r->agent_money}}</td>
        <td>{{$r->platform_money}}</td>
    </tr>
@endforeach