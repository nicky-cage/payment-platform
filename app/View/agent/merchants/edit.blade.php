@extends('agent._layouts.edit')

@section('content')
<div class="layui-row layui-form-item">
    <div class="layui-col-lg6">
        <label class="layui-form-label">商户名称</label>
        <div class="layui-input-inline">
            <input name="merchant_name" lay-verify="required" placeholder="请输入商户名称" autocomplete="off" class="layui-input" 
                value="@if ($r->id == 0)下级商户{{mt_rand(1000, 9999)}}@else{{$r->merchant_name}}@endif" />
        </div>
    </div>
    <div class="layui-col-lg6">
        <label class="layui-form-label">登录账号</label>
        <div class="layui-input-inline">
            <input name="name" lay-verify="required" placeholder="请输入商户登录账号" autocomplete="off" class="layui-input" 
                value="@if ($r->id == 0)MR{{mt_rand(10000, 99999)}}@else{{$r->name}}@endif" @if($r->id != 0) disabled @endif />
        </div>
    </div>
</div>
<div class="layui-row layui-form-item">
    <div class="layui-col-lg6">
        <label class="layui-form-label">手机号码</label>
        <div class="layui-input-inline">
            <input name="phone" lay-verify="required" placeholder="请输入联系手机号码" autocomplete="off" class="layui-input" 
                value="@if ($r->id == 0)13{{mt_rand(10000, 99999)}}{{mt_rand(1000, 9999)}}@else{{$r->phone}}@endif" />
        </div>
    </div>
    <div class="layui-col-lg6">
        <label class="layui-form-label">电子邮箱</label>
        <div class="layui-input-inline">
            <input name="mail" lay-verify="required" placeholder="请输入商户电子邮箱" autocomplete="off" class="layui-input" 
                value="@if ($r->id == 0)AgMer{{mt_rand(10000, 99999)}}{{'@gmail.com'}}@else{{$r->mail}}@endif" />
        </div>
    </div>
</div>
<div class="layui-row layui-form-item">
    <div class="layui-col-lg6">
    </div>
    <div class="layui-col-lg6">
        <label class="layui-form-label label-radio">状态</label>
        <div class="layui-input-inline" style="width: 300px;">
            <input type="radio" name="state" value="1" title="启用" @if ($r->id == 0 || $r->state == 1) checked @endif />
            <input type="radio" name="state" value="0" title="停用" @if ($r->id != 0 && $r->state == 0) checked @endif />
        </div>
    </div>
</div>
<div class="layui-row layui-form-item">
    <div class="layui-col-md6">
        <label class="layui-form-label label-radio">入款权限</label>
        <div class="layui-input-inline">
            <input type="radio" name="pay_in" value="1" title="启用" @if ($r->id == 0 || $r->pay_in == 1) checked @endif />
            <input type="radio" name="pay_in" value="0" title="停用" @if ($r->id != 0 && $r->pay_in == 0) checked @endif />
        </div>
    </div>
    <div class="layui-col-md6">
        <label class="layui-form-label label-radio">出款权限</label>
        <div class="layui-input-inline">
            <input type="radio" name="pay_out" value="0" title="停用" @if ($r->id == 0 || $r->pay_out == 0) checked @endif />
            <input type="radio" name="pay_out" value="1" title="启用" @if ($r->id != 0 && $r->pay_out == 1) checked @endif />
        </div>
    </div>
</div>
@if($r->id == 0)
<div class="layui-row layui-form-item">
    <div class="layui-col-lg6">
        <label class="layui-form-label">登录密码</label>
        <div class="layui-input-inline">
            <input name="password" lay-verify="required" placeholder="请输入登录密码" autocomplete="off" class="layui-input" value="PP{{date('His')+43200}}" />
        </div>
    </div>
    <div class="layui-col-lg6">
        <label class="layui-form-label">重复密码</label>
        <div class="layui-input-inline">
            <input name="re_password" lay-verify="required" placeholder="请输入重复密码" autocomplete="off" class="layui-input" value="PP{{date('His')+43200}}" />
        </div>
    </div>
</div>
@endif
<div class="layui-form-item">
    <label class="layui-form-label">排序</label>
    <div class="layui-input-inline">
        <input type="text" name="sort" lay-verify="required" style="width: 100px;" placeholder="请输入排序" autocomplete="off" class="layui-input" 
            value="@if ($r->id == 0){{1}}@else{{intval($r->sort)}}@endif" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">备注</label>
    <div class="layui-input-inline">
        <input type="text" name="remark" lay-verify="required" style="width: 480px;" placeholder="请输入相关备注" autocomplete="off" class="layui-input" 
            value="@if ($r->id == 0)下级商户添加:{{date('Y年m月d日')}}@else{{$r->remark}}@endif" />
    </div>
</div>
@endsection