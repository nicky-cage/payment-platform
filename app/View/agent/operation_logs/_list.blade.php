@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->admin_id}}</td>
        <td>{{$r->name}}</td>
        <td class="is_show" data-data="{{$r->remark}}" style="cursor: pointer;">{{$r->remark}}</td>
        <td>{{$r->method}}</td>
        <td>{{$r->url}}</td>
        <td>{{$r->operate_ip}}</td>
        <td>{{$r->operate_area}}</td>
        <td>{{$r->created}}</td>
    </tr>
@endforeach

<script>
    layui.use('layer', function(){
        var $ = layui.jquery, layer = layui.layer;
        $(".is_show").click(function () {
            var data = $(this).attr("data-data")
            layer.open({
                type: 1,
                area: ['420px', '240px'], //宽高
                content: data
            });
        })

    });
</script>

