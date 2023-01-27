@extends('backend._layouts.edit')

@section('content')
    <div class="layui-row layui-form-item">
        <div class="layui-col-lg6">
            <label class="layui-form-label">商户名称</label>
            <div class="layui-input-inline">
                <input name="merchant_name" lay-verify="required" placeholder="请输入商户名称" autocomplete="off" class="layui-input"
                       value="@if($r->id == 0){{'商户'}}{{mt_rand(1000, 9999)}}@else{{trim($r->merchant_name)}}@endif"/>
            </div>
        </div>
        <div class="layui-col-lg6">
            <label class="layui-form-label">商户类型</label>
            <div class="layui-input-inline">
                <select name="merchant_type" lay-verify="required" lay-search="">
                    <option value="">请选择商户类型</option>
                    <option value="2" @if(2 == $r->merchant_type || $r->merchat_type == 0)selected="selected"@endif>代理商户</option>
                    <option value="1" @if(1 == $r->merchant_type)selected="selected"@endif>直属商户</option>
                </select>
            </div>
        </div>
    </div>
    <div class="layui-row layui-form-item">
        <div class="layui-col-lg6">
            <label class="layui-form-label">登录账号</label>
            <div class="layui-input-inline">
                <input name="name" lay-verify="required" placeholder="请输入商户登录账号" autocomplete="off" class="layui-input"
                       value="@if ($r->id == 0)A{{chr(mt_rand(65, 90))}}{{mt_rand(100000, 999999)}}@else {{trim($r->name)}} @endif"
                       @if($r->id != 0) disabled @endif />
            </div>
        </div>
        <div class="layui-col-lg6">
            <label class="layui-form-label">电子邮箱</label>
            <div class="layui-input-inline">
                <input name="mail" lay-verify="required" placeholder="请输入商户电子邮箱" autocomplete="off" class="layui-input"
                       value="@if ($r->id == 0)merchant{{mt_rand(10000, 99999)}}{{trim('@gmail.com')}}@else {{$r->mail}} @endif"/>
            </div>
        </div>
    </div>
    <div class="layui-row layui-form-item">
        <div class="layui-col-lg6">
            <label class="layui-form-label">手机号码</label>
            <div class="layui-input-inline">
                <input name="phone" lay-verify="required" placeholder="请输入联系手机号码" autocomplete="off" class="layui-input"
                       value="@if ($r->id == 0)13{{mt_rand(10000, 99999)}}{{mt_rand(1000, 9999)}}@else {{$r->phone}} @endif"/>
            </div>
        </div>
        <div class="layui-col-lg6">
            <label class="layui-form-label">上级代理</label>
            <div class="layui-input-inline">
                <select name="parent_id" lay-verify="required" lay-search="">
                    <option value="0">没有上级代理商户</option>
                    @foreach ($merchants as $merchant)
                        <option value="{{$merchant->id}}" @if ($r->parent_id == $merchant->id)selected="selected"@endif>{{$merchant->merchant_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="layui-row layui-form-item">
        <div class="layui-col-lg6">
            <label class="layui-form-label label-radio">状态</label>
            <div class="layui-input-inline" style="width: 300px;">
                <input type="radio" name="state" value="1" title="启用" @if ($r->id == 0 || $r->state == 1) checked @endif />
                <input type="radio" name="state" value="0" title="停用" @if ($r->id != 0 && $r->state == 0) checked @endif />
            </div>
        </div>
        <div class="layui-col-lg6"> &nbsp;</div>
    </div>
    <div class="layui-row layui-form-item">
        <div class="layui-col-md6">
            <label class="layui-form-label label-radio">入款权限</label>
            <div class="layui-input-inline">
                <input type="radio" name="pay_in" value="1" title="启用" @if ($r->id == 0 || $r->state == 1) checked @endif />
                <input type="radio" name="pay_in" value="0" title="停用" @if ($r->id != 0 && $r->pay_in == 0) checked @endif />
            </div>
        </div>
        <div class="layui-col-md6">
            <label class="layui-form-label label-radio">出款权限</label>
            <div class="layui-input-inline">
                <input type="radio" name="pay_out" value="1" title="启用" @if ($r->id == 0 || $r->state == 1) checked @endif />
                <input type="radio" name="pay_out" value="0" title="停用" @if ($r->id != 0 && $r->pay_out == 0) checked @endif />
            </div>
        </div>
    </div>
    @if($r->id == 0)
        <div class="layui-row layui-form-item">
            <div class="layui-col-lg6">
                <label class="layui-form-label">登录密码</label>
                <div class="layui-input-inline">
                    <input name="password" lay-verify="required" placeholder="请输入登录密码" autocomplete="off" class="layui-input"
                           @if ($r->id == 0) value="{{chr(mt_rand(65, 90))}}{{chr(mt_rand(65, 90))}}{{mt_rand(100000, 999999)}}" @endif />
                </div>
            </div>
            <div class="layui-col-lg6">
                <label class="layui-form-label">排序</label>
                <div class="layui-input-inline">
                    <input type="text" name="sort" lay-verify="required" style="width: 100px;" placeholder="请输入排序" autocomplete="off"
                           class="layui-input" value="@if ($r->id == 0){{'1000'}}@else{{strval($r->sort)}}@endif"/>
                </div>
            </div>
        </div>
    @endif
    <div class="layui-form-item">
        <label class="layui-form-label">备注</label>
        <div class="layui-input-inline">
            <input type="text" name="remark" style="width: 480px;" placeholder="请输入相关备注" autocomplete="off" class="layui-input"
                   value="@if ($r->id == 0){{'商户入驻:'}}{{date('Y年m月d-H时i分s秒')}}@else{{$r->remark}}@endif"/>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">授权IP</label>
        <div class="layui-input-inline">
            <input type="text" name="allow_ip" style="width: 480px;" placeholder="请输入商户登录授权IP, 请用,号隔开" autocomplete="off"
                   class="layui-input" value="@if ($r->id == 0){{'127.0.0.1'}}@else{{$r->allow_ip}}@endif"/>
        </div>
    </div>
@endsection