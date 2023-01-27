@extends('agent._layouts.edit')

@section('content')
<div class="layui-form-item">
    <label class="layui-form-label">账号</label>
    <div class="layui-input-inline">
        <input type="text" name="name" lay-verify="required" placeholder="请输入账号" autocomplete="off" class="layui-input" value="{{$r->name}}" style="width: 380px;" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">密码</label>
    <div class="layui-input-inline">
        <input type="password" name="password" lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input" value="{{$r->password}}" style="width: 380px;" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">昵称</label>
    <div class="layui-input-inline">
        <input type="text" name="nickname" placeholder="请输入昵称" autocomplete="off" class="layui-input" value="{{$r->nickname}}" style="width: 380px;" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">邮箱账号</label>
    <div class="layui-input-inline">
        <input type="text" name="mail" placeholder="请输入邮箱账号" autocomplete="off" class="layui-input" value="{{$r->mail}}" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">授权IP</label>
    <div class="layui-input-inline">
        <textarea class="layui-textarea" lay-verify="required" placeholder="请输入授权IP" name="allow_ips" style="width: 380px;">{{$r->allow_ips}}</textarea><span style="color: orangered">如有多个用英文逗号隔开</span>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">角色</label>
    <div class="layui-input-inline">
        <select name="role_id" lay-verify="required" lay-search="">
            @foreach ($adminRoles as $k=>$v)
            <option value="{{$k}}" @if($k==$r->role_id)selected="selected"@endif>{{$v['name']}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">状态</label>
    <div class="layui-input-inline">
        <input type="radio" name="state" value="0" title="停用" @if($r->state == 0) checked @endif />
        <input type="radio" name="state" value="1" title="启用" @if($r->state == 1) checked @endif />
    </div>
</div>

@endsection