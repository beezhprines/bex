@extends('adminlte::page')

@section('content_header')
<h4>
    Аналитика
    <small class="float-right">
        <x-week-range></x-week-range>
    </small>
</h4>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-warning card-outline">
            <div class="card-body">
                <x-weekplan-progress :profit='$totalComission' :milestones='$milestones'></x-weekplan-progress>
            </div>
        </div>
    </div>
</div>
@stop
