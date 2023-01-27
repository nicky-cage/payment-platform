@extends('backend._layouts.edit')

@section('content')
<div class="layui-form-item">
    <label class="layui-form-label">交易金额</label>
    <div class="layui-input-inline">
        <input type="text" name="amount" lay-verify="required" placeholder="请输入交易金额" autocomplete="off" class="layui-input" value="{{$r->amount}}" style="width: 380px;" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">实付金额</label>
    <div class="layui-input-inline">
        <input type="text" name="amount_paid" lay-verify="required" placeholder="请输入实付金额" autocomplete="off" class="layui-input" value="{{$r->amount_paid}}" style="width: 380px;" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">备注</label>
    <div class="layui-input-inline">
        <textarea name="remark" required lay-verify="required" placeholder="请输入备注" autocomplete="off" class="layui-textarea" style="width: 380px;">{{$r->remark}}</textarea>
    </div>
</div>

@endsection
