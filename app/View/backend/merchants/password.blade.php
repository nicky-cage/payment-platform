@extends('backend._layouts.edit')

@section('content')
    <div class="layui-form-item">
        <label class="layui-form-label">新的密码</label>
        <div class="layui-input-inline">
            <input type="password" name="password" lay-verify="required" placeholder="请输入新的密码" autocomplete="off" class="layui-input" />
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">重复密码</label>
        <div class="layui-input-inline">
            <input type="password" name="re_password" lay-verify="required" placeholder="请输入重复密码" autocomplete="off" class="layui-input" />
        </div>
    </div>
@endsection