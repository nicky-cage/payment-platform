@extends('backend._layouts.edit')

@section('content')
    <div class="layui-form-item">
        <label class="layui-form-label">用户名称</label>
        <div class="layui-input-inline">
            <input type="text" name="" disabled placeholder="" autocomplete="off" class="layui-input" value="{{$info->name}}" />
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">昵称</label>
        <div class="layui-input-inline">
            <input type="text" name="nickname" style="width: 380px;" placeholder="请输入昵称" autocomplete="off" class="layui-input" value="{{$info->nickname}}" />
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label">电子邮箱</label>
        <div class="layui-input-inline">
            <input type="text" name="mail" style="width: 380px;" placeholder="请输入电子邮箱" autocomplete="off" class="layui-input" value="{{$info->mail}}" />
        </div>
    </div>
@endsection
