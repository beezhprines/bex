@extends('adminlte::page')

@section('content_header')
<h4>
    Статистика
    <small class="float-right">
        <x-week-range></x-week-range>
    </small>
</h4>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        @forelse($team->masters as $master)
        @include('masters.week-services', ['master' => $master])
        @empty
        <div class="card">
            <div class="card-body">
                @lang("common.no-data")
            </div>
        </div>
        @endforelse
    </div>
    <div class="col-md-4">
        <div class="card card-info card-outline">
            <div class="card-body">
                <!-- TEAM CONTENTS -->
                @if (!empty($teams))
                @include('teams.team-selects', ['teams' => $teams, 'route' => 'managers.statistics', 'current' => $team])
                @endif

                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>Оператор</b>
                        <span class="float-right">
                            @if (!empty($team->operator))
                            <span class="badge badge-warning" title="Суммарный бонус">
                                {{ $team->operator->getPoints($team->operator->getProfit(week()->start(), week()->end())) }}
                            </span>
                            {{ $team->operator->name }}
                            @else
                            @lang('common.no-data')
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>Конверсия записей</b>
                        <span class="float-right">
                            {{ $team->solveConversion(week()->sunday(isodate(strtotime(isodate() . ' -7 day'))), week()->end(), true) }}%
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>Конверсия пришедших</b>
                        <span class="float-right">
                            {{ $team->solveConversion(week()->sunday(isodate(strtotime(isodate() . ' -7 day'))), week()->end(), false) }}%
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card card-warning card-outline">
            <div class="card-header">
                <strong>Контакты</strong>
                <div class="card-tools">
                    <!-- Collapse Button -->
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body p-0">
                @include('contacts.week-form', ['contactTypes' => $contactTypes])
            </div>
        </div>
    </div>
</div>
@stop
