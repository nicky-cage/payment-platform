@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->name}}</td>
        <td>{{$r->code}}</td>
        <td>{{$r->created}}</td>
        <td>{{$r->updated}}</td>
        <td>
            <input type="checkbox" name="state" lay-skin="switch" lay-text="正常|禁用" class="sp-btn-state" values="1|0"
                   lay-filter="state" value="{{$r->state}}" url="/channels/state" rid="{{$r->id}}"/>
        </td>
        <td>{{$r->remark}}</td>
        <td>
            <button class="layui-btn sp-open-link" url="/{{$controller}}/update" area="750px,600px">编辑</button>
        </td>
    </tr>
@endforeach
