@extends('errors::minimal')

@section('title', __('common.forbidden'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: __('common.forbidden'))))
@section('link')
<a href="/">На главную</a>
@stop
