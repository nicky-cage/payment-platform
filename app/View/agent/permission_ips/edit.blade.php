@extends('agent._layouts.edit')

@section('content')
<div class="layui-form-item">
    <label class="layui-form-label">授权对象ip</label>
    <div class="layui-input-inline">
        <input type="text" name="ip" lay-verify="required" placeholder="请输入菜单名称" autocomplete="off" class="layui-input" value="{{$r->ip}}" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">描述</label>
    <div class="layui-input-inline">
        <input type="text" name="remark" placeholder="请输入链接地址" autocomplete="off" class="layui-input" value="{{$r->remark}}" />
    </div>
</div>
<div class="layui-col-lg6">
    <label class="layui-form-label">状态</label>
    <div class="layui-input-inline" style="width: 300px;">
        <input type="radio" name="state" value="0" title="停用" @if ($r->state == 0) checked @endif />
        <input type="radio" name="state" value="1" title="启用" @if ($r->state == 1) checked @endif />
    </div>
</div>
@endsection