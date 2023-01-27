@foreach ($rows as $k => $r)
    <tr @if ($k == 0)total="{{$total}}"@endif>
        <td>{{$r->id}}</td>
        <td>{{$r->merchant_code}}</td>
        <td><a href="#" class="get-lower" value="{{$r->id}}">{{$r->merchant_name}}</a></td>
        <td>{{$r->parent_name}}</td>
        <td>{{$r->phone}}</td>
        <td>{{$r->mail}}</td>

        <td>
            @if ($r->pay_in == 0)<span style="color: red">已停用</span>@endif
            @if ($r->pay_in == 1)<span style="color: green">已启用</span>@endif
        </td>
        <td>
            @if ($r->pay_out == 0)<span style="color: red">已停用</span>@endif
            @if ($r->pay_out == 1)<span style="color: green">已启用</span>@endif
        </td>
        <td>
            @if ($r->state == 0)<span class="red">已禁用</span>@endif
            @if ($r->state == 1)<span class="green">已启用</span>@endif
        </td>
        <td>{{$r->sort}}</td>
        <td>
            <button class="layui-btn sp-open-link" url="/{{$controller}}/update" area="750px,480px">编辑</button>
            <button class="layui-btn sp-open-link layui-bg-orange" url="/{{$controller}}/password" area="600px,300px">重置密码</button>
        </td>
    </tr>
@endforeach
<script>
    layui.use(['jquery', 'form', 'layer'], function () {

        let form = layui.form,
            $ = layui.jquery,
            layer = layui.layer;

        $(".get-lower").click(function (data) {
			
            let id = $(this).attr("value");
            layer.open({
                type: 2,
                area: ['1000px', '600px'],
                //fix: true, //不固定
                shade: 0.5,
                title: "下级代理",
                content: "/merchants/lower?id=" + id
            })
            // $.ajax({
            //     type: "get",
            //     url: "/merchants/lower",
            //     data: {"id": id},
            //     success: function (data) {
            //         console.log(data)
            //         layer.open({
            //             type: 1,
            //             area: ['960', '600px'], //宽高
            //             content: data
            //         });
            //     }
            // })
        })
    })
</script>
