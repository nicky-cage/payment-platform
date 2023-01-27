@extends('backend._layouts.list')

@section('content')
    @component('backend._slots.panel')
    <div class="layui-card-body">
        <button class="layui-btn sp-open-link" url="/{{$controller}}/create" area="750px,750px">新增角色</button>
    </div>
    @endcomponent
    @component('backend._slots.panel')
        <div class="layui-card-body">
            <table class="layui-table">
                <colgroup>
                    <col width="80" />
                    <col width="150" />
                    <col />
                    <col width="165" />
                    <col width="165" />
                    <col width="170" />
                </colgroup>
                <thead>
                <tr>
                    <th>序号</th>
                    <th>角色名称</th>
                    <th>备注</th>
                    <th>添加时间</th>
                    <th>修改时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="sp-loaded-table" loaded="loaded" url="/admin_roles">
                    @include('backend.admin_roles._list')
                </tbody>
            </table>
        </div>
    @endcomponent
@endsection
