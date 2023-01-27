@extends('backend._layouts.list')

@section('content')
    <div class="layui-form-item">
        <label class="layui-form-label">持卡姓名</label>
        <div class="layui-input-inline">
            <input type="text" name="name" lay-verify="required" placeholder="请输入持卡姓名" autocomplete="off" class="layui-input" value="{{$r->real_name}}" />
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">单笔最高</label>
        <div class="layui-input-inline">
            <input type="text" name="url" lay-verify="required" placeholder="请输入单笔最高" autocomplete="off" class="layui-input" value="{{$r->each_max}}" style="width: 150px;" />
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">单笔最低</label>
        <div class="layui-input-inline">
            <input type="text" name="url" lay-verify="required" placeholder="请输入单笔最低" autocomplete="off" class="layui-input" value="{{$r->each_min}}" style="width: 150px;" />
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">最高支付</label>
        <div class="layui-input-inline">
            <input type="text" name="icon" lay-verify="required" placeholder="请输入最高支付限额" autocomplete="off" class="layui-input" value="{{$r->pay_max}}" style="width: 150px;" />
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">最多调用</label>
        <div class="layui-input-inline">
            <input type="text" name="remark" lay-verify="required" style="width: 150px;" placeholder="请输入最多调用次数" autocomplete="off" class="layui-input" value="{{$r->call_count}}" />
        </div>
    </div>
@endsection