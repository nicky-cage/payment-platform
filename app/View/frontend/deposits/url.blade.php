@extends('frontend._layouts.base')

@section('content')
<a href="{!! $url !!}" target="_blank">正在跳转支付页面, 如果1秒钟后跳转失败, 请手动点击此连接 ...</a>
@if ($auto_redirect)
<script>
    location.href = "{!! $url !!}";
</script>
@endif
@endsection