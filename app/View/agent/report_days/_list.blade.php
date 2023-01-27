@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->merchant_name}}</td>
        <td>{{$r->day}}</td>
        <td>{{$r->pay_money}}</td>
        <td>{{$r->pay_num}}</td>
        <td>{{$r->pay_cost}}</td>
        <td>{{$r->withdraw_money}}</td>
        <td>{{$r->withdraw_num}}</td>
        <td>{{$r->withdraw_cost}}</td>
        <td>{{$r->agent_money}}</td>
    </tr>
@endforeach