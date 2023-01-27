<!DOCTYPE html>
<html>

<head>@include('agent._elements.head')</head>
<style>
    .layui-form-radio * {
        font-size: 12px;
    }

    .label-radio {
        margin-top: 12px;
    }
</style>
<script src="{{$STATIC_URL}}/js/scripts.js"></script>
<body>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body" style="margin-bottom: 62px;">
                    <form class="layui-form" method="post" action="/{{$controller}}/{{$action ?? 'save'}}">
                        @yield('content')
                        <div class="layui-layout-admin" style="z-index:99;">
                            <input type="hidden" value="{{($r->id ?? ($id ?? '0'))}}" name="id"/>
                            <div class="layui-input-block layui-footer" style="margin-left: 0px; left: 0px;">
                                <button type="submit" class="layui-btn" lay-submit lay-filter="sp-save">立即提交</button>
                                <button type="button" class="layui-btn layui-btn-danger sp-btn-cancel" lay-filter="cancel">取消操作</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>