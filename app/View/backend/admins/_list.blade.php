@foreach ($rows as $k => $r)
    @if ($r->name != 'admin')
        <tr @if ($k==0) total="{{ $total }}" @endif>
            <td>{{ $r->id }}</td>
            <td>{{ $r->name }}</td>
            <td>{{ $r->nickname }}</td>
            <td>{{ $r->mail }}</td>
            <td>{{ $r->allow_ips }}</td>
            <td>{{ $r->last_ip}}</td>
            <td>{{ $r->role_name }}</td>
            <td>{{ $r->created }}</td>
            <td>@if ($r->last_login > 0){{ date('Y-m-d H:i:s', $r->last_login) }}@else{{'-'}}@endif</td>
            <td>
                <input type="checkbox" name="state" lay-skin="switch" lay-text="正常|禁用" class="sp-btn-state" values="1|0"
                       lay-filter="state" value="{{$r->state}}" url="/admins/state" rid="{{$r->id}}"/>
            </td>
            <td>
                <button class="layui-btn sp-open-link" url="/admins/update" type="button" area="600px,540px">编辑</button>
            </td>
        </tr>
    @endif
@endforeach