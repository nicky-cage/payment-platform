@foreach ($rows as $k => $r)
<tr @if ($k==0)total="{{$total}}" @endif>
    <td>{{$r->id}}</td>
    <td>{{$paymentTypes[$r->payment_type] ?? '-'}}</td>
    <td>{{$r->name}}</td>
    <td>
        @if ($r->state == 0) <span style="color:red">禁用</span>
        @else
        @if ($r->state == 1) <spans style="color: green">正常 </span> @else {{'-'}} @endif
            @endif
    </td>
    <td>@if ($r->amount_min == 0) - @else {{ $r->amount_min }}@endif</td>
    <td>@if ($r->amount_max == 0) - @else {{$r->amount_max}}@endif</td>
    <td>{{$r->amounts}}</td>
    <td>{{$r->rate_lower}}</td>
    <td>{{$r->updated}}</td>
    <td> </td>
</tr>
@endforeach