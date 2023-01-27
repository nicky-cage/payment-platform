@foreach ($rows as $k => $r)
<tr @if ($k==0)total="{{$total}}" @endif>
    <td>{{$r->id}}</td>
    <td>{{$r->name}}</td>
    <td>{{$r->code}}</td>
    <td>{{$r->image}}</td>
    <td>{{$r->sort}}</td>
    <td>{{$r->state}}</td>
    <td></td>
    <td>
        <button class="layui-btn sp-open-link" url="/{{$controller}}/update" area="750px,500px">编辑</button>
    </td>
</tr>
@endforeach