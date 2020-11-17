@extends('adminlte::page')

@section('content_header')
<x-week-header header="Диаграммы"></x-week-header>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-warning card-outline">
            <div class="card-body">
                <x-weekplan-progress :profit='$totalComission' :milestones='$milestones' :hideMilestones="true"></x-weekplan-progress>
            </div>
        </div>
    </div>
</div>
@stop
