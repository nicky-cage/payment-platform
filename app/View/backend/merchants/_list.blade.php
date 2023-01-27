@foreach ($rows as $k => $r)
    <tr @if ($k==0)total="{{$total}}" @endif>
        <td>{{$r->id}}</td>
        <td>{{$r->merchant_code}}</td>
        <td>{{$r->merchant_name}}</td>
        <td>{{$r->name}}</td>
        <td>{{$r->parent_name}}</td>
        <td>{{$r->phone}}</td>
        <td>{{$r->mail}}</td>
        <td>
            @if ($r->merchant_type == 1) <span style="color: green">直属商户</span>
            @elseif ($r->merchant_type == 2) <span style="color: red">代理商户</span>
            @else -
            @endif
        </td>
        <td>
            <input type="checkbox" name="pay_in" lay-skin="switch" lay-text="正常|禁用" class="sp-btn-state" values="1|0"
                   lay-filter="state" value="{{$r->pay_in}}" url="/merchants/state" rid="{{$r->id}}"/>
        </td>
        <td>
            <input type="checkbox" name="pay_out" lay-skin="switch" lay-text="正常|禁用" class="sp-btn-state" values="1|0"
                   lay-filter="state" value="{{$r->pay_out}}" url="/merchants/state" rid="{{$r->id}}"/>
        </td>
        <td>
            <input type="checkbox" name="state" lay-skin="switch" lay-text="正常|禁用" class="sp-btn-state" values="1|0"
                   lay-filter="state" value="{{$r->state}}" url="/merchants/state" rid="{{$r->id}}"/>
        </td>
        <td>
            <input type="checkbox" name="google_verify" lay-skin="switch" lay-text="正常|禁用" class="sp-btn-state" values="1|0"
                   lay-filter="state" value="{{$r->google_verify}}" url="/merchants/google_verify" rid="{{$r->id}}"/>
        </td>
        <td>
            @if ($r->parent_id == 0)
                <button class="layui-btn layui-btn-danger sp-open-link"
                        url="/{{$controller}}/payments?merchant_id={{$r->id}}" area="92%,92%">设置点位
                </button>
            @endif
            <button class="layui-btn sp-open-link" url="/{{$controller}}/update" area="750px,500px">编辑</button>
            <button class="layui-btn sp-open-link layui-bg-orange" url="/{{$controller}}/password" area="600px,250px">重置密码</button>
        </td>
    </tr>
@endforeach