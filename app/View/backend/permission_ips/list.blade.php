@extends('backend._layouts.list')

@section('content')
    @component('backend._slots.panel')
        <form class="layui-form" lay-filter="" tbody="0">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">授权对象</label>
                        <div class="layui-input-inline">
                            <input type="text" name="ip" placeholder="请输入IP地址" autocomplete="off" class="layui-input"/>
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
            <button class="layui-btn sp-open-link" url="/{{$controller}}/create" area="600px,300px">新增授权IP</button>
        </div>
    @endcomponent

    @component('backend._slots.panel')
        <div class="layui-card-body">
            <table class="layui-table">
                <colgroup>
                    <col width="80"/>
                    <col width="150"/>
                    <col width="200"/>
                    <col/>
                    <col width="130"/>
                    <col width="130"/>
                    <col width="100"/>
                    <col width="110"/>
                </colgroup>
                <thead>
                <tr>
                    <th>序号</th>
                    <th>授权类型</th>
                    <th>授权对象</th>
                    <th>描述</th>
                    <th>添加时间</th>
                    <th>最后修改时间</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('backend.permission_ips._list')</tbody>
            </table>
        </div>
    @endcomponent
@endsection