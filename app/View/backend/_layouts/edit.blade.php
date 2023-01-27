<!DOCTYPE html>
<html>

<head>@include('backend._elements.head')</head>
<style>
    .layui-form-radio * {
        font-size: 12px;
    }

    .label-radio {
        margin-top: 12px;
    }
</style>

<body>
    <div class="layui-fluid">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md12">
                <div class="layui-card">
                    <div class="layui-card-body" style="margin-bottom: 62px;">
                        <form class="layui-form" method="post" action="/{{$controller}}/{{$action ?? 'save'}}">
                            @yield('content')
                            <div class="layui-layout-admin" style="z-index:99;">
                                <input type="hidden" value="{{($r->id ?? ($id ?? '0'))}}" name="id" />
                                <div class="layui-input-block layui-footer" style="margin-left: 0px; left: 0px; padding-top: 15px;">
                                    <button type="button" class="layui-btn layui-btn-danger sp-btn-cancel" 
                                        style="margin-left: 15px; margin-right: 15px; float: right;" lay-filter="cancel">取消操作</button>
                                    <button type="submit" class="layui-btn" lay-submit lay-filter="sp-save" style="float: right;">立即提交</button>
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
<script src="/static/js/scripts.js"></script>
