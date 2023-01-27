@extends('frontend._layouts.base')

@section('content')
    <form name="form_pay" action="{{$url}}" method="post" id="form-pay" target="_self">
        @foreach ($inputs as $k => $v)
            <input type="hidden" name="{{$k}}" value="{{$v}}"/>
        @endforeach
        <input type="submit" value="立即支付" class="layui-btn-normal" id="submit-pay"/>
    </form>
    <script>
        layui.use(['jquery'], function () {
            let $ = layui.jquery;
            // -- 自动支付跳转
            (function () {
                $("#submit-pay").val("正在跳转支付 ...");
                $("#form-pay").submit();
            })();
        });
    </script>
@endsection