@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->name}}</td>
        <td>{{$r->code}}</td>
        <td>{{$r->encrypt_type}}</td>
        <td>{{$r->state}}</td>
        <td>{{$r->created}}</td>
        <td>{{$r->updated}}</td>
        <td>{{$r->remark}}</td>
        <td>
            <button class="layui-btn sp-open-link" url="/{{$controller}}/update" area="750px,480px">编辑</button>
        </td>
    </tr>
@endforeach
