@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->channel_id}}</td>
        <td>{{$r->deposit_start}} - {{$r->deposit_end}}</td>
        <td>{{$r->deposit_min}} - {{$r->deposit_max}}</td>
        <td>{{$r->withdraw_start}} - {{$r->withdraw_end}}</td>
        <td>{{$r->withdraw_min}} - {{$r->withdraw_max}}</td>
        <td>
            <button class="layui-btn sp-open-link" url="/{{$controller}}/update" area="750px,480px">编辑</button>
        </td>
    </tr>
@endforeach