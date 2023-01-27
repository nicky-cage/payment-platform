@foreach ($rows as $k => $r)
    <tr @if ($k == 0) total="{{ $total }}" @endif>
        <td>{{ $r->id }}</td>
        <td>{{ $merchants[$r->merchant_id] }}</td>
        <td>{{ $r->name }}</td>
        <td>
            @if ($r->state == 0)<span class="red">已停用</span>@endif
            @if ($r->state == 1)<span class="green">已启用</span>@endif
        </td>
        <td>
            @if ($r->pay_in == 0)<span class="red">已停用</span>@endif
            @if ($r->pay_in == 1)<span class="green">已启用</span>@endif
        </td>
        <td>
            @if ($r->pay_out == 0)<span class="red">已停用</span>@endif
            @if ($r->pay_out == 1)<span class="green">已启用</span>@endif
        </td>
        <td>{{ $r->allow_ips }}</td>
        <td>{{ $r->notify_url }}</td>
        <td>{{ $r->notify_url_payout }}</td>
        <td>
            @if ($r->updated != 0)
                {{ \Carbon\Carbon::parse($r->updated + 8 * 3600)->format('Y-m-d H:i') }} @endif
        </td>
        <td>
            <button class="layui-btn sp-open-link" url="/{{ $controller }}/update" area="750px,650px">编辑</button>
            <button class="layui-btn reset-secret layui-btn-danger" pid="{{ $r->id }}"
                url="/{{ $controller }}/secret" area="600px,600px">重置密钥</button>
        </td>
    </tr>
@endforeach
