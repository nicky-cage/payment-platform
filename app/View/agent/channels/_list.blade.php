@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->name}}</td>
        <td>{{$r->code}}</td>
        <td>
            @if ($r->state == 1) <span style="color: green">启用</span>
            @elseif ($r->state == 0) <span style="color: red">停用</span>
            @else {{$statusTypes[$r->state]}}
            @endif
        </td>
        <td>{{$r->created}}</td>
        <td>{{$r->updated}}</td>
        <td>{{$r->remark}}</td>
        <td>
            <button class="layui-btn sp-open-link" url="/{{$controller}}/update" area="750px,600px">编辑</button>
        </td>
    </tr>
@endforeach
