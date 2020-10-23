@extends('adminlte::page')

@section('content_header')
<h4>
    Аналитика
    <small class="float-right">
        @include('components.tools.weekrange')
    </small>
</h4>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-warning card-outline">
            <div class="card-body">
                <x-tools.weekplan-progress :profit='$totalComission' :milestones='$milestones'></x-tools.weekplan-progress>
            </div>
        </div>
    </div>
</div>
@stop