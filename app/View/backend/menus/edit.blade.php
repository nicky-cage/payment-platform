@extends('backend._layouts.edit')

@section('content')
<div class="layui-inline">
    <label class="layui-form-label">上级菜单</label>
    <div class="layui-input-inline">
        <div class="layui-input-inline">
            <select name="parent_id" lay-verify="required" lay-search="">
                @foreach ($rootMenus as $k=>$v)
                    <option value="{{$v->id}}" @if($v->id == $r->parent_id)selected="selected"@endif>{{$v->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">菜单名称</label>
    <div class="layui-input-inline">
        <input type="text" name="name" lay-verify="required" placeholder="请输入菜单名称" autocomplete="off" class="layui-input" value="{{$r->name}}" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">菜单级别</label>
    <div class="layui-input-inline">
        <select name="level" lay-verify="required" lay-search="">
            <option value="">请选择菜单级别</option>
            @foreach ($menuLevels as $k => $v)
                <option value="{{$k}}" @if($k == $r->level)selected="selected"@endif>{{$k}} - {{$v}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">链接地址</label>
    <div class="layui-input-inline">
        <input type="text" name="url" lay-verify="required" placeholder="请输入链接地址" autocomplete="off" class="layui-input" value="{{$r->url}}" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">是否显示</label>
    <div class="layui-input-inline">
        <select name="state" lay-verify="required" lay-search="">
            <option value="0" @if ($r->state == 0) selected="selected" @endif>否</option>
            <option value="1" @if ($r->state == 1) selected="selected" @endif>是</option>
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">图标样式</label>
    <div class="layui-input-inline">
        <input type="text" name="icon"  placeholder="请输入图标样式" autocomplete="off" class="layui-input" value="{{$r->icon}}" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">备注</label>
    <div class="layui-input-inline">
        <input type="text" name="remark"  style="width: 380px;" placeholder="请输入相关备注" autocomplete="off" class="layui-input" value="{{$r->remark}}" />
    </div>
</div>
@endsection