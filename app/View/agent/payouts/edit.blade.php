@extends('agent._layouts.edit')

@section('content')
<div class="layui-form-item">
    <label class="layui-form-label">提现银行</label>
    <div class="layui-input-inline">
        <select name="bank_code" lay-verify="required" class="layui-select">
            @foreach ($banks as $bank)
            <option value="{{$bank->code}}">{{$bank->name}}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">持卡姓名</label>
    <div class="layui-input-inline">
        <input type="text" name="name" lay-verify="required" placeholder="请输入持卡人姓名" autocomplete="off" class="layui-input" value="" style="width: 190px;" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">银行卡号</label>
    <div class="layui-input-inline">
        <input type="text" name="bank_card" lay-verify="required" placeholder="请输入银行卡号" autocomplete="off" class="layui-input" value="" style="width: 190px;" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">支行名称</label>
    <div class="layui-input-inline">
        <input type="text" name="bank_branch" lay-verify="required" placeholder="请输入支行名称" autocomplete="off" class="layui-input" value="银行支行名称" style="width: 190px;" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">提现金额</label>
    <div class="layui-input-inline">
        <input type="text" name="amount" lay-verify="required" placeholder="请输入提现金额" autocomplete="off" class="layui-input" value="0" style="width: 100px;" />
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">备注</label>
    <div class="layui-input-inline">
        <textarea name="remark" required lay-verify="required" placeholder="请输入备注" autocomplete="off" class="layui-textarea" style="width: 380px;">请尽快处理</textarea>
    </div>
</div>
<div class="layui-form-item">
    <label class="layui-form-label">谷歌验证</label>
    <div class="layui-input-inline">
        <input type="text" name="google_code" lay-verify="required" placeholder="请输入Google验证码" autocomplete="off" class="layui-input" style="width: 190px;" />
    </div>
</div>

<input type="hidden" value="{{$ip}}" name="ip" />
@endsection