@extends('backend._layouts.edit')

@section('content')
<div class="layui-form-item">
    <label class="layui-form-label label-radio">状态</label>
    <div class="layui-input-inline">
        <input type="radio" name="state" value="0" title="停用" @if ($r->state == 0) checked @endif />
        <input type="radio" name="state" value="1" title="启用" @if ($r->state == 1) checked @endif />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label label-radio">轮询状态</label>
    <div class="layui-input-inline">
        <input type="radio" name="polling" value="0" title="否" @if ($r->polling == 0) checked @endif />
        <input type="radio" name="polling" value="1" title="是" @if ($r->polling == 1) checked @endif />
    </div>
</div>

@endsection
