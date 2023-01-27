@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->merchant_name}}</td>
        <td>{{$r->order_number}}</td>
        <td>{{$r->merchant_id}}</td>
        <td>{{$r->merchant_name}}</td>
        <td>{{$r->merchant_account}}</td>
        <td>{{$r->card_number}}</td>
        <td>{{$r->payer_name}}</td>
        <td>{{$r->bank_order_number}}</td>
        <td>{{$r->paid_amount}}</td>
        <td>{{$statusTypes[$r->state] ?? '-'}}</td>
        <td>{{$r->finished}}</td>
        <td></td>
    </tr>
@endforeach