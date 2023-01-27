@extends('backend._layouts.edit')

@section('content')
<div class="layui-form-item">
    <label class="layui-form-label">电话号码</label>
    <div class="layui-input-inline">
        <input type="text" name="phone" style="width: 190px;" placeholder="请输入电话号码" autocomplete="off" class="layui-input" value="{{$info->phone}}" />
    </div>
</div>

<div class="layui-form-item">
    <label class="layui-form-label">电子邮箱</label>
    <div class="layui-input-inline">
        <input type="text" name="mail" style="width: 380px;" placeholder="请输入电子邮箱" autocomplete="off" class="layui-input" value="{{$info->mail}}" />
    </div>
</div>
@endsection