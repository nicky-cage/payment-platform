@extends('backend._layouts.edit')

@section('content')
    <div class="layui-form-item">
        <label class="layui-form-label">银行名称</label>
        <div class="layui-input-inline">
            <select name="bank_id" lay-verify="required" lay-search="">
                @foreach ($banks as $k=>$v)
                    <option value="{{$k}}" @if($k == $r->bank_id)selected="selected"@endif>{{$v}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">支行信息</label>
        <div class="layui-input-inline">
            <input type="text" name="branch_name" lay-verify="required" placeholder="请输入支行信息" autocomplete="off" class="layui-input" value="{{$r->branch_name}}" style="width: 380px;"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">银行卡号</label>
        <div class="layui-input-inline">
            <input type="text" name="card_number" lay-verify="required" placeholder="请输入银行卡号" autocomplete="off" class="layui-input" value="{{$r->card_number}}" style="width: 380px;"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">持卡姓名</label>
        <div class="layui-input-inline">
            <input type="text" name="real_name" lay-verify="required" placeholder="请输入持卡姓名" autocomplete="off" class="layui-input" value="{{$r->real_name}}"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">单笔最高</label>
        <div class="layui-input-inline">
            <input type="text" name="each_max" lay-verify="required" placeholder="请输入单笔最高" autocomplete="off" class="layui-input" value="{{$r->each_max}}" style="width: 150px;"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">单笔最低</label>
        <div class="layui-input-inline">
            <input type="text" name="each_min" lay-verify="required" placeholder="请输入单笔最低" autocomplete="off" class="layui-input" value="{{$r->each_min}}" style="width: 150px;"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">最高支付</label>
        <div class="layui-input-inline">
            <input type="text" name="pay_max" lay-verify="required" placeholder="请输入最高支付限额" autocomplete="off" class="layui-input" value="{{$r->pay_max}}" style="width: 150px;"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">最多调用</label>
        <div class="layui-input-inline">
            <input type="text" name="call_count" lay-verify="required" style="width: 150px;" placeholder="请输入最多调用次数" autocomplete="off" class="layui-input" value="{{$r->call_count}}"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">启用</label>
        <div class="layui-input-inline">
            <select name="status">
                <option></option>
                <option value="0" @if($r->status==0) selected @endif>否</option>
                <option value="1" @if($r->status==1) selected @endif>是</option>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">总额度</label>
        <div class="layui-input-inline">
            <input type="text" name="amount" lay-verify="required" style="width: 150px;" placeholder="请输入总额度" autocomplete="off" class="layui-input" value="{{$r->amount}}"/>
        </div>
    </div>
@endsection