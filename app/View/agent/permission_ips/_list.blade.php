@foreach ($rows as $k => $r)
    <tr @if ($k == 0) total="{{ $total }}" @endif>
        <td>{{ $r->id }}</td>
        <td>允许访问</td>
        <td>{{ $r->ip }}</td>
        <td>{{ $r->remark }}</td>
        <td>{{ \Carbon\Carbon::parse($r->updated + 8 * 3600)->format('Y-m-d H:i:s') }}</td>
        <td>
            @if ($r->state == 0)<span class="red">禁用</span>@endif
            @if ($r->state == 1)<span class="green">正常</span>@endif

        </td>
        <td>
            <button class="layui-btn sp-open-link" url="/permission_ips/update" area="600px,450px">编辑</button>
            <button class="layui-btn sp-btn-delete layui-btn-danger" url="/permission_ips/delete">删除</button>
        </td>
    </tr>
@endforeach
