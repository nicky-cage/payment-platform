@extends('agent._layouts.edit')

@section('content')
<div class="layui-form-item">
    <label class="layui-form-label">角色名称</label>
    <div class="layui-input-inline">
        <input type="text" name="name" lay-verify="required" placeholder="请输入角色名称" autocomplete="off" class="layui-input" value="{{$r->name}}" style="width: 380px;" />
        <input type="hidden" id="id" value="{{$r->id}}">
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">角色权限</label>
    <div class="layui-input-inline">
        <div id="AdminRole" class="demo-tree-more AdminRole"></div>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">备注</label>
    <div class="layui-input-inline">
        <input type="text" name="remark" lay-verify="required" placeholder="请输入备注" autocomplete="off" class="layui-input" value="{{$r->remark}}" style="width: 380px;" />
    </div>
</div>
<script>
    layui.use(['jquery', 'layer', 'tree'], function() {
        let $ = layui.jquery,
            layer = layui.layer,
            tree = layui.tree;
        $(function() {
            $.post("/admin_roles/all", {
                "id": $("#id").val()
            }, function(res) {
                tree.render({
                    elem: '#AdminRole',
                    data: res.data,
                    showCheckbox: true //是否显示复选框
                        ,
                    id: 'RoleId',
                    isJump: true //是否允许点击节点时弹出新窗口跳转
                });
                return res.data;
            })
        })
    })
</script>
@endsection