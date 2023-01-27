@extends('backend._layouts.base')

@section('content')
    <div class="layadmin-tips">
        <i class="layui-icon" face>&#xe664;</i>
        <div class="layui-text" style="font-size: 20px;">
            {{$message}} |
            <a href="/index/index">返回首页</a>
        </div>
    </div>
    <script>
        layui.use(['jquery'], function () {
            if (top != window) {
                top.location.href = "/index/error?message={{$message}}";
            }
        });
    </script>
@endsection