@extends('backend._layouts.edit')

@section('content')
    <div class="layui-form-item">
        <label class="layui-form-label">当前密码</label>
        <div class="layui-input-inline">
            <input type="password" name="password" style="width: 380px;" placeholder="请输入6到16个字符密码" autocomplete="off" class="layui-input" />
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">新的密码</label>
        <div class="layui-input-inline">
            <input type="password" name="password_new" style="width: 380px;" placeholder="请输入6到16个字符密码" autocomplete="off" class="layui-input" />
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">确认密码</label>
        <div class="layui-input-inline">
            <input type="password" name="password_rep" style="width: 380px;" placeholder="请输入6到16个字符密码" autocomplete="off" class="layui-input" />
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">谷歌验证</label>
        <div class="layui-input-inline">
            <input type="google_code" name="google_code" style="width: 380px;" placeholder="请输入Google验证码" autocomplete="off" class="layui-input" />
        </div>
    </div>
@endsection
