@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->name}}</td>
        <td class="show-text" text="<pre>{{print_r(json_decode($r->remark), true)}}</pre>" style="cursor: pointer;">{{\App\Common\Utils::truncate($r->remark, 64)}}</td>
        <td>{{$r->method}}</td>
        <td>{{$r->url}}</td>
        <td>{{$r->operate_ip}}</td>
        <td>{{$r->operate_area}}</td>
        <td>{{$r->created}}</td>
    </tr>
@endforeach
