@extends('agent._layouts.base')

@section('content')
<div class="layadmin-tips">
    <i class="layui-icon" face>&#xe664;</i>
    <div class="layui-text" style="font-size: 20px;">
        {{$message}}
    </div>
</div>
@endsection