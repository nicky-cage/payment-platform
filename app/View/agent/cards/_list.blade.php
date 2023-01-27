@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->bank_name}}</td>
        <td>{{$r->bank_code}}</td>
        <td>{{$r->branch_name}}</td>
        <td>{{$r->card_number}}</td>
        <td>{{$r->real_name}}</td>
        <td>{{$r->each_min}}</td>
        <td>{{$r->each_max}}</td>
        <td>{{$r->pay_max}}</td>
        <td>{{$r->call_count}}</td>
        <td>{{$r->amount}}</td>
        <td>{{$r->created}}</td>
        <td>{{$r->updated}}</td>
        <td></td>
    </tr>
@endforeach