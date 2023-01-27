@extends('backend._layouts.list')

@section('content')
    @component('backend._slots.panel')
        <form class="layui-form" lay-filter="" tbody="0">
            <div class="layui-form layui-card-header layuiadmin-card-header-auto">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">渠道名称</label>
                        <div class="layui-input-inline">
                            <input type="text" name="name" placeholder="请输入渠道名称" autocomplete="off" class="layui-input"/>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">渠道编码</label>
                        <div class="layui-input-inline">
                            <input type="text" name="code" placeholder="请输入渠道编码" autocomplete="off" class="layui-input"/>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">备注</label>
                        <div class="layui-input-inline">
                            <input type="text" name="remark" placeholder="请输入相关备注" autocomplete="off" class="layui-input"/>
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
            <button class="layui-btn sp-open-link" url="/{{$controller}}/create" area="750px,600px">新增渠道</button>
        </div>
    @endcomponent

    @component('backend._slots.panel')
        <div class="layui-card-body">
            <table class="layui-table">
                <colgroup>
                    <col width="60"/>
                    <col width="150"/>
                    <col width="150"/>
                    <col width="130"/>
                    <col width="130"/>
                    <col width="100"/>
                    <col/>
                    <col width="60"/>
                </colgroup>
                <thead>
                <tr>
                    <th>序号</th>
                    <th>渠道名称</th>
                    <th>渠道编码</th>
                    <th>添加时间</th>
                    <th>修改时间</th>
                    <th>状态</th>
                    <th>备注</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('backend.channels._list')</tbody>
            </table>
        </div>
    @endcomponent
@endsection