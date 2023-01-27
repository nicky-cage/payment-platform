@extends('backend._layouts.edit')

@section('content')
<div class="layui-form-item">
    <label class="layui-form-label">&nbsp;</label>
    <div class="layui-input-inline">
        <img src="{{$url}}" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">&nbsp;</label>
    <div class="layui-input-inline" style="width: 800px;">
        <ul>
            <li><span style="color: green; font-weight: bold; font-size: 20px;" id="counter">00</span> 秒之后, 当前Google验证码将会刷新 !</li>
            <li style="color:red">*** 请务必在计数完成之前扫码, 并点击 <span style="font-weight: bold; color: green">"立即提交"</span> 按钮以绑定谷歌验证码 ***</li>
            <li>如果你已经绑定过谷歌验证密钥, 可以重新扫码绑定, 或者直接关闭此页面 </li>
        </ul>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">&nbsp;</label>
    <div class="layui-input-inline">
        <input name="test_value" class="layui-input" id="test_value" placeholder="请输入谷歌验证码" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">&nbsp;</label>
    <div class="layui-input-inline">
        <button type="button" class="layui-btn" value="验证谷歌绑定" id="test-bind">测试已经绑定的谷歌验证码</button>
    </div>
</div>
<input type="hidden" name="google_secret" value="{{$secret}}" />
<script>
    layui.use(['jquery'], function() {
        let $ = layui.jquery;
        (function() {
            let current = (new Date()).getSeconds();
            current = current >= 30 ? 60 - current : 30 - current;
            $("#counter").text(current >= 10 ? current : '0' + current);
            setInterval(function() {
                current -= 1;
                if (current <= 0) {
                    location.reload();
                }
                $("#counter").text(current >= 10 ? current : '0' + current);
            }, 1000);
        })();

        $(document).on("click", "#test-bind", function() {
            let test_value = $("#test_value").val();
            if (test_value == "") {
                sp.alert("必须输入验证码");
                return;
            }
            sp.post("/index/google_test", {
                "value": test_value
            }, function(result) {
                if (result.code == 0) {
                    sp.alertSuccess("验证成功");
                } else {
                    sp.alert("验证失败");
                }
            });
        });
    });
</script>
@endsection