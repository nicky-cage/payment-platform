@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->parent_id}}</td>
        <td>{{$r->parent_id}}</td>
        <td>{{$r->name}}</td>
        <td>{{$r->url}}</td>
        <td></td>
        <td>{{$r->icon}}</td>
        <td>{{$statusTypes[$r->state]}}</td>
        <td>{{$r->level}} - {{App\Helpers\Helper::menuLevel($r->level)}}</td>
        <td>{{$r->remark}}</td>
        <td>{{$r->sort}}</td>
        <td>
            <button class="layui-btn sp-open-link" url="/{{$controller}}/update" area="600px,600px">编辑</button>
        </td>
    </tr>
@endforeach