@extends('backend._layouts.list')

@section('content')
    @component('backend._slots.panel')
        <div style="padding: 10px 15px;">
            <div class="layui-card-body" style="padding: 0px;">
                <table class="layui-table">
                    <colgroup>
                        <col width="150"/>
                        <col width="300"/>
                        <col width="150"/>
                        <col/>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>商户编号</th>
                        <th>商户名称</th>
                        <th>通道余额</th>
                        <th>备注</th>
                    </tr>
                    </thead>
                    <tbody id="sp-loaded-table" loaded="loaded" url="/{{$controller}}/list">@include('backend.merchant_channels._list')</tbody>
                </table>
            </div>
        </div>
    @endcomponent
@endsection