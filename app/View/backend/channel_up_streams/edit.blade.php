@extends('backend._layouts.edit')

@section('content')
    <div class="layui-form-item">
        <label class="layui-form-label">通道名称</label>
        <div class="layui-input-inline">
            <select name="code" lay-verify="required" lay-search="">
                @foreach ($channels as $k=>$v)
                    <option value="{{$v['code']}}" @if($v['code'] == $r->code)selected="selected"@endif>{{$v['name']}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">优先序号</label>
        <div class="layui-input-inline">
            <input type="text" name="priority" lay-verify="required" placeholder="请输入单笔最低" autocomplete="off" class="layui-input" value="{{$r->priority}}" style="width: 100px;" />
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">回调IP</label>
        <div class="layui-input-inline">
            <input type="text" name="callback_ip" lay-verify="required" placeholder="请输入回调IP" autocomplete="off" class="layui-input" value="{{$r->callback_ip}}" style="width: 150px;" />
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label label-radio">状态</label>
        <div class="layui-input-inline">
            <input type="radio" name="state" value="0" title="停用" @if($r->state == 0) checked @endif />
            <input type="radio" name="state" value="1" title="启用" @if($r->state == 1) checked @endif />
        </div>
    </div>
@endsection
