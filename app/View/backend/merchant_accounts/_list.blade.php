@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->merchant_name}}</td>
        <td>{{$r->app_count}}</td>
        <td>{{$r->order_count}}</td>
        <td>{{$r->order_valid}}</td>
        <td>{{$r->order_success}}</td>
        <td>{{$r->order_total}}</td>
        <td>{{$r->order_total_valid}}</td>
        <td>{{$r->total_withdraw}}</td>
        <td>{{sprintf('%.2f', $r->total)}}</td>
        <td>{{sprintf('%.2f', $r->remain)}}</td>
        <td>{{sprintf('%.2f', $r->frozen)}}</td>
        <td>{{sprintf('%.2f', $r->total_in)}}</td>
        <td>{{sprintf('%.2f', $r->total_out)}}</td>
        <td>{{sprintf('%.2f', $r->real_merchant)}}</td>
        <td>{{sprintf('%.2f', $r->real_platform)}}</td>
        <td>{{sprintf('%.2f', $r->real_platform - $r->real_merchant)}}</td>
        <td>
            <button class="layui-btn layui-btn-danger">清理订单</button>
            <button class="layui-btn layui-btn-orange">清空账户</button>
        </td>
    </tr>
@endforeach
