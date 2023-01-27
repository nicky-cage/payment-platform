@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->name}}</td>
        <td>{{$r->code}}</td>
        <td>{{$statusTypes[$r->state] ?? '-'}}</td>
        <td>{{$r->created ? $r->created : '-'}}</td>
        <td>
            <button class="layui-btn sp-open-link" url="/{{$controller}}/update" area="600px,300px">编辑</button>
        </td>
    </tr>
@endforeach