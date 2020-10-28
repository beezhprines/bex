@extends('adminlte::page')

@section('content_header')
<h4>
    Недельный план

    <span class="float-right">
        <x-week-range></x-week-range>
    </span>
</h4>
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
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            {{ $manager->name }}
                            <span class="badge badge-primary">{{ $manager->premium_rate * 100 }}%</span>
                        </span>
                        <strong>
                            {{ price($manager->getBonus(week()->start(), week()->end())) }}
                        </strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-8">
        <div class="card card-secondary card-outline">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <th>Мастер</th>
                            <th class="text-center">
                                Комиссия за неделю (KZT)
                            </th>
                            <th class="text-center">
                                Бонусы за неделю (KZT)
                            </th>
                            <th>
                                Количество услуг
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
                                    @endphp
                                    {{ price($masterComission) }}
                                </td>
                                <td class="text-center">
                                    {{ price($master->solveManagerBonus($masterComission, $comission, $managerBonus)) }}
                                </td>
                                <td class="text-center">
                                    {{ $master->getRecords(week()->start(), week()->end())->count() }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
    });
</script>
@stop
