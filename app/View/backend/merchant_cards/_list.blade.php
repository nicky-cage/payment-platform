@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->merchant_id}}</td>
        <td>{{$r->merchant_name}}</td>
        <td>{{$r->card_id}}</td>
        <td>{{$r->card_number}}</td>
        <td>{{$statusTypes[$r->state]}}</td>
        <td>{{$pollingTypes[$r->polling]}}</td>
        <td>
            <button class="layui-btn sp-open-link" url="/{{$controller}}/update" area="750px,270px">编辑</button>
        </td>
    </tr>
@endforeach
