@extends('backend._layouts.base')

@section('content')
<script>
    if (top != window) top.location.href = location.href;
</script>
<link rel="stylesheet" href="/static/layuiadmin/style/login.css" media="all" />
<div class="layadmin-user-login layadmin-user-display-show" id="LAY-user-login" style="display: none;">
    <div class="layadmin-user-login-main">
        <div class="layadmin-user-login-box layadmin-user-login-header">
            <h2>后台管理系统</h2>
            <p>Backend Backstage Management System</p>
        </div>
        <div class="layadmin-user-login-box layadmin-user-login-body layui-form">
            <div class="layui-form-item">
                <label class="layadmin-user-login-icon layui-icon layui-icon-username" style="line-height: 28px;"></label>
                <input type="text" name="username" id="username" lay-verify="required" placeholder="请输入用户用户名称" class="layui-input" />
            </div>
            <div class="layui-form-item">
                <label class="layadmin-user-login-icon layui-icon layui-icon-password" style="line-height: 28px;"></label>
                <input type="password" name="password" id="password" lay-verify="required" placeholder="请输入用户登录密码" class="layui-input" />
            </div>
            <div class="layui-form-item">
                <label class="layadmin-user-login-icon layui-icon layui-icon-key" style="line-height: 28px;"></label>
                <input type="password" name="google_code" id="google_code" lay-verify="required" placeholder="请输入Google验证码" class="layui-input" />
            </div>
            <div class="layui-form-item">
                <div class="layui-row">
                    <div class="layui-col-xs7">
                        <label class="layadmin-user-login-icon layui-icon layui-icon-vercode" for="LAY-user-login-vercode" style="line-height: 28px;"></label>
                        <input type="text" name="verify_code" id="verify_code" lay-verify="required" placeholder="请输入图形验证码" class="layui-input" />
                    </div>
                    <div class="layui-col-xs5">
                        <div style="margin-left: 10px;" id="verify_img_container"></div>
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-btn-fluid" lay-submit lay-filter="login" id="submit-login">登 入</button>
            </div>
        </div>
    </div>
</div>
<script>
    layui.use(['jquery', 'form', 'layer'], function() {

        let form = layui.form,
            $ = layui.jquery,
            layer = layui.layer;
        // 显示google验证码
        let showVerifyCode = function() {
            $.get("/index/code", function(result) {
                let img = "<img src='" + result + "' style='cursor: pointer' />";
                $("#verify_img_container").html(img);
            });
        };
        showVerifyCode();
        $(document).on("click", "#verify_img_container", function() {
            showVerifyCode();
        });

        form.on('submit(login)', function(data) { // 登录后台管理系统
            sp.post("/index/login", data.field, function(result) {
                if (result.code === 0) { // 如果登录成功
                    layui.layer.msg("登录成功", {
                        icon: 1,
                        timeout: 1000
                    }, function() {
                        location.href = "/index/manage";
                    });
                }
            });
            return false;
        });

        $(document).on("keyup", "#verify_code", function(evt) {
            if (evt.keyCode == 13) {
                $("#submit-login").trigger("click");
            }
        });
    });
</script>
@endsection