@foreach ($rows as $k => $r)
    <tr @if ($k==0) total="{{ $total }}" @endif>
        <td>{{ $r->id }}</td>
        <td>允许访问</td>
        <td>{{ $r->ip }}</td>
        <td>{{ $r->remark }}</td>
        <td>{{ \Carbon\Carbon::parse($r->updated + 8 * 3600)->format('Y-m-d H:i:s') }}</td>
        <td>{{ \Carbon\Carbon::parse($r->updated + 8 * 3600)->format('Y-m-d H:i:s') }}</td>
        <td>
            <input type="checkbox" name="state" lay-skin="switch" lay-text="正常|禁用" class="sp-btn-state" values="1|0"
                   lay-filter="state" value="{{$r->state}}" url="/permission_ips/state" rid="{{$r->id}}"/>
        </td>
        <td>
            <button class="layui-btn sp-open-link" url="/permission_ips/update" area="600px,300px">编辑</button>
            <button class="layui-btn sp-btn-delete layui-btn-danger" url="/permission_ips/delete">删除</button>
        </td>
    </tr>
@endforeach