@extends('backend._layouts.list')

@section('content')
    @component('backend._slots.panel')
        <form class="layui-form" lay-filter="" tbody="0">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">菜单名称</label>
                        <div class="layui-input-inline">
                            <input type="text" name="name" placeholder="请输入菜单名称" autocomplete="off" class="layui-input" />
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">菜单级别</label>
                        <div class="layui-input-inline">
                            <select name="level">
                                <option value="">请选择菜单级别</option>
                                @foreach ($menuLevels as $k => $v)
                                    <option value="{{$k}}">{{$k}} - {{$v}}</option>
                                @endforeach
                            </select>
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
            <button class="layui-btn sp-open-link" url="/{{$controller}}/create" area="750px,650px">新增</button>
        </div>
    @endcomponent

    @component('backend._slots.panel')
        <div class="layui-card-body">
            <table class="layui-table">
                <colgroup>
                    <col width="80" />
                    <col width="150" />
                    <col width="150" />
                    <col width="220" />
                    <col width="200" />
                    <col width="80" />
                    <col width="100" />
                    <col width="80" />
                    <col width="120" />
                    <col />
                    <col width="100" />
                    <col width="100" />
                </colgroup>
                <thead>
                <tr>
                    <th>序号</th>
                    <th>上级菜单序号</th>
                    <th>上级菜单名称</th>
                    <th>菜单名称</th>
                    <th>链接地址</th>
                    <th>显示</th>
                    <th>图标</th>
                    <th>状态</th>
                    <th>级别</th>
                    <th>备注</th>
                    <th>排序</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="sp-loaded-table" loaded="loaded" url="/menus">@include('backend.menus._list')</tbody>
            </table>
        </div>
    @endcomponent
@endsection
