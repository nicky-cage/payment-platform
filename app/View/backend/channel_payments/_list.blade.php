@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        @if ($r->pay_count != 0)
            <td rowspan="{{$r->pay_count}}">{{$r->channel_id}} / {{$r->channels_name ?? '-'}}</td>
        @endif
        <td>{{$r->code}}</td>
        <td>{{$paymentTypes[$r->payment_type] ?? '-'}}</td>
        <td>{{$r->name}}</td>
        <td>{{$statusTypes[$r->state] ?? '-'}}</td>
        <td>@if ($r->amount_min == 0) - @else {{ $r->amount_min }}@endif</td>
        <td>@if ($r->amount_max == 0) - @else {{$r->amount_max}}@endif</td>
        <td>{{$r->amounts}}</td>
        <td>
            <input type="checkbox" name="state" lay-skin="switch" lay-text="正常|禁用" class="sp-btn-state" values="1|0"
                   lay-filter="state" value="{{$r->state}}" url="/channel_payments/state" rid="{{$r->id}}"/>
        </td>
        <td>{{$r->rate}}</td>
        <td>{{$r->updated}}</td>
        <td>
            <button class="layui-btn sp-open-link" url="/{{$controller}}/update" area="750px,550px">编辑</button>
        </td>
    </tr>
@endforeach