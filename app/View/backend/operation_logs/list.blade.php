@extends('backend._layouts.list')

@section('content')
    @component('backend._slots.panel')
        <form class="layui-form" lay-filter="" tbody="0">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">用户名称</label>
                        <div class="layui-input-inline">
                            <input type="text" name="name" placeholder="请输入用户名称" autocomplete="off" class="layui-input"/>
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
    @endcomponent

    @component('backend._slots.panel')
        <div class="layui-card-body">
            <table class="layui-table">
                <colgroup>
                    <col width="60"/>
                    <col width="120"/>
                    <col/>
                    <col width="80"/>
                    <col width="250"/>
                    <col width="150"/>
                    <col width="300"/>
                    <col width="135"/>
                </colgroup>
                <thead>
                <tr>
                    <th>序号</th>
                    <th>用户名称</th>
                    <th>备注</th>
                    <th>方法</th>
                    <th>来源URL</th>
                    <th>操作IP</th>
                    <th>操作地区</th>
                    <th>操作时间</th>
                </tr>
                </thead>
                <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('backend.operation_logs._list')</tbody>
            </table>
        </div>
    @endcomponent
    <script>
        layui.use('layer', function () {
            let $ = layui.jquery, layer = layui.layer;
            $(".is_show").click(function () {
                let data = $(this).attr("data-data")
                layer.open({
                    type: 1,
                    area: ['420px', '240px'], //宽高
                    content: data
                });
            })
        });
    </script>
@endsection