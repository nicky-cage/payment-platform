@foreach ($rows as $k => $r)
    <tr @if ($k == 0) total="{{ $total }}" @endif>
        <td>{{ $r->id }}</td>
        <td>{{ $merchants[$r->merchant_id] }}</td>
        <td>{{ $r->name }}</td>
        <td>{{ $r->merchant_name }}</td>
        <td>
            <input type="checkbox" name="pay_in" lay-skin="switch" lay-text="正常|禁用" class="sp-btn-state" values="1|0"
                   lay-filter="state" value="{{$r->pay_in}}" url="/merchant_apps/state" rid="{{$r->id}}"/>
        </td>
        <td>
            <input type="checkbox" name="pay_out" lay-skin="switch" lay-text="正常|禁用" class="sp-btn-state" values="1|0"
                   lay-filter="state" value="{{$r->pay_out}}" url="/merchant_apps/state" rid="{{$r->id}}"/>
        </td>
        <td>
            <input type="checkbox" name="state" lay-skin="switch" lay-text="正常|禁用" class="sp-btn-state" values="1|0"
                   lay-filter="state" value="{{$r->state}}" url="/merchant_apps/state" rid="{{$r->id}}"/>
        </td>
        <td>{{ $r->allow_ips }}</td>
        <td>{{ $r->remark}}</td>
        <td>
            @if ($r->created != 0) {{ \Carbon\Carbon::parse($r->created + 8 * 3600)->format('Y-m-d H:i') }}
            @else -
            @endif
        </td>
        <td>
            @if ($r->updated != 0) {{ \Carbon\Carbon::parse($r->updated + 8 * 3600)->format('Y-m-d H:i') }}
            @else -
            @endif
        </td>
        <td>
            <button class="layui-btn sp-open-link" url="/{{ $controller }}/update" area="750px,450px">编辑</button>
            <button class="layui-btn reset-secret layui-btn-danger" pid="{{ $r->id }}"
                    url="/{{ $controller }}/secret" area="600px,600px">重置密钥
            </button>
        </td>
    </tr>
@endforeach