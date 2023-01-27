@foreach ($rows as $k => $r)
    <tr @if ($k == 0) total="{{ $total }}" @endif>
        <td>{{ $r->id }}</td>
        <td>{{ $r->name }}</td>
        <td>{{ $r->nickname }}</td>
        <td>{{ $r->mail }}</td>
        <td>{{ $r->allow_ips }}</td>
        <td>{{ $r->role_name }}</td>
        <td>{{ $r->created }}</td>
        <td>{{ Carbon\Carbon::parse($r->last_login + 8 * 3600)->format('Y-m-d H:i:s') }}</td>
        <td>
            @if ($r->state == 0)<span class="red">禁用</span>@endif
            @if ($r->state == 1)<span class="green">正常</span>@endif
        </td>
        <td>
            <button class="layui-btn sp-open-link" url="/admins/update" type="button" area="600px,540px">编辑</button>
            <button class="layui-btn layuiadmin-btn-list layui-btn-danger sp-btn-delete" tab="0" url="/admins/delete"
                data-type="add">删除</button>
        </td>
    </tr>
@endforeach
