@extends('backend._layouts.list')

@section('content')
    <div class="layui-col-md12">
        <div class="layui-card">
            <div class="layui-card-body">
                <div class="layui-tab" lay-filter="sp-lazy-load">
                    <ul class="layui-tab-title">
                        <li class="layui-this">按日统计</li>
                        <li>按月统计</li>
                        <!--<li>按年统计</li>-->
                    </ul>
                    <div class="layui-tab-content">
                        <div class="layui-tab-item layui-show" url="/report_days">
                            @include('backend.report_days.i_list')
                        </div>
                        <div class="layui-tab-item" url="/report_months">
                            @include('backend.report_months.list')
                        </div>
                        <!--<div class="layui-tab-item" url="/report_years">
                                backend.report_years.list'
                                </div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection