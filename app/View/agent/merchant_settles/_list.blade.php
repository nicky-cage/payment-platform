@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->merchant_id}}</td>
        <td>{{$r->merchant_name}}</td>
        <td>{{$r->amount}}</td>
        <td>{{$r->fee}}</td>
        <td>{{$r->amount_settled}}</td>
        <td>{{$r->card_number}}/{{$r->real_name}}</td>
        <td>{{$province[$r->province_id]['name']}}/{{$city[$r->city_id]['name']}}/{{$district[$r->district_id]['name']}}</td>
        <td>{{$statusTypes[$r->state]}}</td>
        <td>{{date("Y-m-d H:i:s",$r->finished)}}</td>
        <td>
            <button class="layui-btn sp-open-link" url="/{{$controller}}/update" area="880px,650px">编辑</button>
        </td>
    </tr>
@endforeach
