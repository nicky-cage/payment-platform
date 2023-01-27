@extends('backend._layouts.edit')

@section('content')
    <div class="layui-form-item">
        <label class="layui-form-label" style="margin-top: 12px;">状态</label>
        <div class="layui-input-block">
            <input type="radio" name="state" value="0" title="待付" @if ($r->state == 0) checked @endif />
            <input type="radio" name="state" value="1" title="实付" @if ($r->state == 1) checked @endif />
            <input type="radio" name="state" value="2" title="取消" @if ($r->state == 2) checked @endif />
            <input type="radio" name="state" value="3" title="其他" @if ($r->state == 3) checked @endif />
        </div>
    </div>
@endsection
