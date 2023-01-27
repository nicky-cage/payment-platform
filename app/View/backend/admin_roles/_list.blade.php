@foreach ($rows as $k => $r)
    <tr @if ($k == 0) total="{{ $total }}" @endif>
        <td>{{$r->id}}</td>
        <td>{{$r->name}}</td>
        <td>{{$r->remark}}</td>
        <td>{{$r->created}}</td>
        <td>{{$r->updated}}</td>
        <td>
            <button class="layui-btn sp-open-link" url="/admin_roles/update" type="button" area="750px,750px">编辑</button>
            <button class="layui-btn layui-btn-danger sp-btn-delete" type="button" url="/admin_roles/delete">删除</button>
        </td>
    </tr>
@endforeach
