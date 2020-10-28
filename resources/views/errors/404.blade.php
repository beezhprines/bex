@extends('errors::minimal')

@section('title', __('common.not-found'))
@section('code', '404')
@section('message', __('common.not-found'))
@section('link')
<a href="/">На главную</a>
@stop

