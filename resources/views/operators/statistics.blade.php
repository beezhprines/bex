@extends('adminlte::page')

@section('content_header')
<x-week-header header="Статистика"></x-week-header>
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
        <div class="card card-outline card-info">
            <div class="card-body">
                @include('teams.team-selects', ['teams' => $teams, 'route' => 'operators.statistics', 'current' => $team])

                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>Текущая неделя</b>
                        <span class="float-right">
                            {{ $team->operator->solvePointsPerTeam($team, week()->start(), week()->end()) }}
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>Прошлая неделя</b>
                        <span class="float-right">
                            {{ $team->operator->solvePointsPerTeam($team, week()->previous(week()->start()), week()->previous(week()->end())) }}
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>Конверсия</b>
                        <span class="float-right">
                            {{ $conversion }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@stop
