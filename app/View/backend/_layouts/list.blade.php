<!DOCTYPE html>
<html>

<head>
    @include('backend._elements.head')
</head>

<body>
    <div class="layui-fluid">
        <div class="layui-col-md12">
            @yield('content')
        </div>
    </div>
</body>

</html>
<script src="/static/js/scripts.js"></script>
<script>
    layui.use(['jquery', 'layer'], function() {
        let $ = layui.jquery, layer = layui.layer;
        $(document).on('click', '.show-text', function() {
            let that = $(this), text = that.attr('text');
            layer.open({
                content: text,
                area: ['60%', '60%'],
            });
        });
    });
</script>
