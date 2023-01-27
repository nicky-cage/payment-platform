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
        <td>@if($r->status==0)未启用 @else 启用 @endif</td>

        <td>{{$r->created}}</td>
        <td>{{$r->updated}}</td>
        <td>
            <button class="layui-btn sp-open-link" url="/{{$controller}}/update" area="750px,500px">编辑</button>
        </td>
    </tr>
@endforeach