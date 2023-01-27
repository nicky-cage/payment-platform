@extends('agent._layouts.edit')

@section('content')
<div class="layui-form-item">
    <label class="layui-form-label">产品名称</label>
    <div class="layui-input-inline">
        <input name="name" lay-verify="required" placeholder="请输入产品名称" autocomplete="off" class="layui-input" value="{{$r->name}}" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">产品编码</label>
    <div class="layui-input-inline">
        <input name="code" lay-verify="required" placeholder="请输入产品编码" autocomplete="off" class="layui-input" value="{{$r->code}}" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">状态</label>
    <div class="layui-input-inline">
        <select name="state" lay-verify="required">
            <option value="0" @if($r->state == 0) selected="selected" @endif>禁用</option>
            <option value="1" @if($r->state == 1) selected="selected" @endif>启用</option>
        </select>
    </div>
</div>
@endsection