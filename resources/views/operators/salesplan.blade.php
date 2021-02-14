@extends('adminlte::page')

@section('content_header')
<x-week-header header="План продаж"></x-week-header>
@stop

@section('content')
<div class="row">
    <div
        @if (!auth()->user()->isChiefOperator() )
        class="col-md-8"
        @else
        class="col-md-12"
        @endif

        >
        <div class="card card-success card-outline collapsed-card">
            <div class="card-header">
                <div class="card-title">
                    Бонусы менеджеров
                </div>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                </div>
            </div>
            <div class="card-body py-0 px-1">
                <ul class="list-group list-group-flush">
                    @foreach($managers as $manager)
                        @if (
                        (auth()->user()->isOwner() || auth()->user()->isHost())
                        ||
                        (auth()->user()->manager->id == $manager->id)
                        )
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            {{ $manager->name }}
                            <span class="badge badge-primary">{{ $manager->premium_rate * 100 }}%</span>
                            <br>
                            <small>{{ $manager->user->role->title }}</small>
                        </span>
                                <strong>
                                    {{ price($manager->getBonus(week()->start(), week()->end())) }}
                                </strong>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="card card-warning card-outline">
            <div class="card-body">
                <div class="mb-2">
                    <x-weekplan-progress :profit='$points' :milestones='$milestones'></x-weekplan-progress>
                </div>
                <div>
                    @if (!auth()->user()->isChiefOperator() )
                    Итого: <strong>{{ $profit }}</strong> KZT
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if (!auth()->user()->isChiefOperator() )
    <div class="col-md-4">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <div class="card-title">
                    Детализация по мастерам
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <th>
                                Мастер
                            </th>
                            <th>
                                Баллы
                            </th>
                        </thead>
                        <tbody>
                            @foreach($masters as $master)
                            <tr>
                                <td>
                                    {{ $master->name }}
                                </td>
                                <td>
                                    {{ $master->solveOperatorsPoints(week()->start(), week()->end()) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@stop
