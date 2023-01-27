@extends('agent._layouts.edit')

@section('content')
<div class="layui-form-item">
    <label class="layui-form-label">代付通道ID</label>
    <div class="layui-input-inline">
        <input type="text" name="up_stream_id" lay-verify="required" placeholder="请输入持卡姓名" autocomplete="off" class="layui-input" value="{{$r->up_stream_id}}" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">代付通道名称</label>
    <div class="layui-input-inline">
        <input type="text" name="up_stream_name" lay-verify="required" placeholder="请输入单笔最高" autocomplete="off" class="layui-input" value="{{$r->up_stream_name}}" style="width: 150px;" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">代付费率</label>
    <div class="layui-input-inline">
        <input type="text" name="fee" lay-verify="required" placeholder="请输入单笔最低" autocomplete="off" class="layui-input" value="{{$r->fee}}" style="width: 150px;" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">代付最低费率</label>
    <div class="layui-input-inline">
        <input type="text" name="fee_min" lay-verify="required" placeholder="请输入最高支付限额" autocomplete="off" class="layui-input" value="{{$r->fee_min}}" style="width: 150px;" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">编码</label>
    <div class="layui-input-inline">
        <input type="text" name="code" lay-verify="required" style="width: 150px;" placeholder="请输入最多调用次数" autocomplete="off" class="layui-input" value="{{$r->code}}" />
    </div>
</div>

@endsection