@extends('agent._layouts.edit')

@section('content')
<div class="layui-form-item">
    <label class="layui-form-label">支行信息</label>
    <div class="layui-input-inline">
        <input type="text" name="branch_name" lay-verify="required" placeholder="请输入支行信息" autocomplete="off" class="layui-input" value="{{$r->branch_name}}" style="width: 380px;" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">银行卡号</label>
    <div class="layui-input-inline">
        <input type="text" name="card_number" lay-verify="required" placeholder="请输入银行卡号" autocomplete="off" class="layui-input" value="{{$r->card_number}}" style="width: 380px;" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">持卡姓名</label>
    <div class="layui-input-inline">
        <input type="text" name="real_name" lay-verify="required" placeholder="请输入持卡姓名" autocomplete="off" class="layui-input" value="{{$r->real_name}}" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">单笔最高</label>
    <div class="layui-input-inline">
        <input type="text" name="each_max" lay-verify="required" placeholder="请输入单笔最高" autocomplete="off" class="layui-input" value="{{$r->each_max}}" style="width: 150px;" />
    </div>
</div>
@endsection