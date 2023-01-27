@extends('agent._layouts.list')

@section('content')
    @component('agent._slots.panel')

    @endcomponent

    @component('agent._slots.panel')
        <div class="layui-card-body">
            <table class="layui-table">
                <colgroup>
                    <col width="60"/>
                    <col width="180"/>
                    <col width="150"/>
                    <col width="150"/>
                    <col/>
                    <col width="100"/>
                    <col width="80"/>
                    <col width="80"/>
                    <col width="60"/>

                </colgroup>
                <thead>
                <tr>
                    <th>序号</th>
                    <th>商户编号</th>
                    <th>商户名称</th>
                    <th>上级商户</th>
                    <th>手机号码</th>
                    <th>电子邮件</th>
                    <!-- <th>账户余额</th>
                     <th>账户冻结余额</th>-->
                    <th>入款权限</th>
                    <th>出款权限</th>
                    <th>状态</th>


                </tr>
                </thead>
                <tbody id="sp-loaded-table" loaded="loaded">
                @foreach ($rows as $k => $r)
                    <tr>
                        <td>{{$r->id}}</td>
                        <td>{{$r->merchant_code}}</td>
                        <td><a href="#" class="get-lower">{{$r->merchant_name}}</a></td>
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
                        })
                    })
                </script>
                </tbody>
            </table>
        </div>
    @endcomponent
@endsection