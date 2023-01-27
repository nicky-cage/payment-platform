<!DOCTYPE html>
<html>

<head>@include('backend._elements.head')</head>

<body>
    <div class="layui-fluid">
        @yield('content')
    </div>
</body>

</html>
<script src="{{$STATIC_URL}}/js/scripts.js"></script>