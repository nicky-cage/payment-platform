@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->url}}</td>
        <td>{{$r->ip}}</td>
        <td class="show-text" text="{{$r->data}}" style="cursor: pointer;">点击查看</td>
        <td>{{$r->created}}</td>
        <td>{{$r->error}}/{{$r->remark}}</td>
        <td></td>
    </tr>
@endforeach
