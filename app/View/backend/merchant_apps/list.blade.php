@extends('backend._layouts.list')

@section('content')
    @component('backend._slots.panel')
        <form class="layui-form" lay-filter="" tbody="0">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">应用名称</label>
                        <div class="layui-input-inline">
                            <input type="text" name="name" placeholder="请输入应用名称" autocomplete="off" class="layui-input"/>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn" lay-submit lay-filter="sp-form-search">
                            <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <div class="layui-card-body">
            <button class="layui-btn sp-open-link" url="/{{$controller}}/create" area="750px,615px">新增应用</button>
        </div>
    @endcomponent

    @component('backend._slots.panel')
        <div class="layui-card-body">
            <table class="layui-table">
                <colgroup>
                    <col width="80"/>
                    <col width="120"/>
                    <col width="150"/>
                    <col width="150"/>
                    <col width="80"/>
                    <col width="80"/>
                    <col width="80"/>
                    <col width="250"/>
                    <col/>
                    <col width="130"/>
                    <col width="130"/>
                    <col width="130"/>
                </colgroup>
                <thead>
                <tr>
                    <th>应用编号</th>
                    <th>商户名称</th>
                    <th>应用名称</th>
                    <th>商户账号</th>
                    <th>状态</th>
                    <th>入款权限</th>
                    <th>出款权限</th>
                    <th>授权IP</th>
                    <th>备注</th>
                    <th>添加时间</th>
                    <th>最后修改</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('backend.merchant_apps._list')</tbody>
            </table>
        </div>
        <script>
            layui.use(['jquery', 'layer'], function () {
                let $ = layui.jquery,
                    layer = layui.layer;
                $(document).on("click", ".reset-secret", function () {
                    let id = $(this).attr("pid");
                    layer.confirm("你确认要重置密钥么?", {
                        icon: 6
                    }, function () {
                        sp.post("/merchant_apps/secret", {
                            id: id
                        }, function (result) {
                            console.log(result);
                            if (result.code == 0) {
                                layer.alert('新的密钥: \n' + result.data.key);
                            }
                        });
                    });
                });
            });
        </script>
    @endcomponent
@endsection