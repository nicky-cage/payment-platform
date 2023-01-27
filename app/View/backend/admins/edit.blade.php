@extends('backend._layouts.edit')

@section('content')
<div class="layui-form-item">
    <label class="layui-form-label">账号</label>
    <div class="layui-input-inline">
        @if ($r->id > 0) {{$r->name}}
        @else
        <input name="name" lay-verify="required" placeholder="请输入管理员账号" autocomplete="off" class="layui-input" value="{{$r->name}}" />
        @endif
    </div>
</div>
@if ($r->id == 0)
<div class="layui-form-item">
    <label class="layui-form-label">密码</label>
    <div class="layui-input-inline">
        <input name="password" lay-verify="required" placeholder="请输入登录密码" autocomplete="off" class="layui-input" value="{{\App\Common\Utils::getPassword()}}" />
    </div>
</div>
@endif
<div class="layui-form-item">
    <label class="layui-form-label">昵称</label>
    <div class="layui-input-inline">
        <input name="nickname" placeholder="请输入昵称" autocomplete="off" class="layui-input" value="@if ($r->id == 0){{sprintf('nickname_%03d', mt_rand(100, 999))}}@else{{$r->nickname}}@endif" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">邮箱账号</label>
    <div class="layui-input-inline">
        <input name="mail" placeholder="请输入邮箱账号" autocomplete="off" class="layui-input" value="@if ($r->id == 0){{sprintf('cdadmin%04d@gmail.com', mt_rand(1000, 9999))}}@else{{$r->mail}}@endif" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">授权IP</label>
    <div class="layui-input-inline">
        <textarea class="layui-textarea" lay-verify="required" placeholder="请输入授权IP" name="allow_ips" style="width: 380px;">@if ($r->id == 0){{'127.0.0.1'}}@else{{$r->allow_ips}}@endif</textarea>
        <span style="color: orangered">如有多个用英文逗号隔开</span>
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
    <div class="layui-input-inline" style="margin-top: -10px">
        <input type="radio" name="state" value="1" title="启用" @if($r->id == 0 || $r->state == 1) checked @endif />
        <input type="radio" name="state" value="0" title="停用" @if($r->id > 0 && $r->state == 0) checked @endif />
    </div>
</div>

@endsection