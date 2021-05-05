@extends('adminlte::page')

@section('content_header')
<x-week-header header="Недельный план">
    <div class="btn-group dropleft">
        <button type="button" class="btn btn-tool btn-transparent btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu">
            <x-sync></x-sync>
            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('loader').style.display = 'block'; document.getElementById('cache-clear-form').submit();">
                Очистить кэш
            </a>
            <form id="cache-clear-form" action="{{ route('cache.clear') }}" method="POST">
                @csrf
            </form>
        </div>
    </div>
</x-week-header>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-success card-outline">
            <div class="card-body">
                <div class="mb-2">
                    <x-weekplan-progress :profit='$comission' :milestones='$milestones'></x-weekplan-progress>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4 p-2">
                        {{ $managerBonusRate * 100 }}% от суммы: <strong>{{ price($comission * $managerBonusRate) }} </strong>
                    </div>
                    <div class="col-md-4 text-center p-2">
                        Недельный бонус: <strong>{{ price($milestoneBonus) }}</strong>
                    </div>
                    <div class="col-md-4 text-right p-2">
                        @php
                        $managerBonus = $comission * $managerBonusRate + $milestoneBonus;
                        @endphp
                        Итого: <strong>{{ price($managerBonus) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-secondary card-outline">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <th class="align-middle">Мастер</th>
                            <th class="text-center align-middle">
                                Комиссия за неделю (KZT)
                            </th>
                            <th class="text-center align-middle">
                                Бонусы за неделю (KZT)
                            </th>
                            <th class="text-center align-middle">
                                Количество услуг <br>
                                <small class="text-muted">
                                    по записям (по услугам)
                                </small>
                            </th>
                        </thead>
                        <tbody>
                            @foreach($masters as $master)
                            <tr>
                                <td>
                                    {{ $master->name }}
                                </td>
                                <td class="text-center">
                                    @php
                                    $masterComission = $master->solveComission(week()->start(), week()->end());
                                    $masterComission = $masterComission + $master->getUnexpectedComission(week()->start(), week()->end());
                                    @endphp
                                    {{ price($masterComission) }}
                                </td>
                                <td class="text-center">
                                    {{ price($master->solveManagerBonus($masterComission, $comission, $managerBonus)) }}
                                </td>
                                <td class="text-center">
                                    @php
                                    $recordsCount = $master->getRecords(week()->start(), week()->end())->count();
                                    $recordsCountByservice = $master->getRecordByService(week()->start(), week()->end())->count();
                                    @endphp
                                    <span class="@if ($recordsCount != $recordsCountByservice) bg-danger @endif">
                                        {{ $recordsCount }}
                                        ({{ $recordsCountByservice }})
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
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
            <div class="card-header">
                <div class="card-title">
                    Бонусы операторов
                </div>
            </div>
            <div class="card-body py-0 px-1">
                <ul class="list-group list-group-flush">
                    @foreach($operators as $operator)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            {{ $operator->name }}
                        </span>
                        <strong>
                            {{ price($operator->getProfit(week()->start(), week()->end())) }}
                        </strong>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="card card-red card-outline">
            <div class="card-header">
                <div class="card-title">
                    Блок ошибок
                </div>
            </div>
            <div class="card-body py-0 px-1">
                <ul class="list-group list-group-flush">
                    @foreach($mastersWithOutTeam as $masterWT)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            Мастер {{ $masterWT->name }}
                        </span>
                            <strong>
                                Нет команды
                            </strong>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="card-body py-0 px-1">
                <ul class="list-group list-group-flush">
                    @foreach($teamWithOutTown as $t)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            {{ $t->title }}
                        </span>
                            <strong>
                                Нет города
                            </strong>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="card-body py-0 px-1">
                <ul class="list-group list-group-flush">
                    @foreach($teamWithOutOper as $t)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            {{ $t->title }}
                        </span>
                            <strong>
                                Нет оператора
                            </strong>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $(".progress-container .milestones .milestone-next")
            .first()
            .css({
                fontWeight: 'bold',
                top: '6px'
            });

        let day = parseInt('{{ request()->query("day") ?? 0}}');

        if (day > 0 && day < 7) {
            $('#loader').css({
                display: 'block'
            });
            $('#sync-form').submit();
        }
    });
</script>
@stop
