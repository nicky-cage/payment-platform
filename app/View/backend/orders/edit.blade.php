@extends('backend._layouts.edit')

@section('content')
    <div class="layui-form-item">
        <label class="layui-form-label">备注</label>
        <div class="layui-input-inline">
            <textarea name="remark" required lay-verify="required" placeholder="请输入备注" autocomplete="off" class="layui-textarea" style="width: 380px;">{{$r->remark}}</textarea>
        </div>
    </div>
@endsection
