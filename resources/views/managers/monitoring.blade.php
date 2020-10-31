@extends('adminlte::page')

@section('content_header')
<x-week-header header="Мониторинг"></x-week-header>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-secondary card-outline">
            <div class="card-header">
                <div class="card-title">
                    Суммы комиссий по неделям (тыс)
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <th class="text-center">Дата</th>
                            @foreach(week()->weekTitles() as $ru)
                            <th class="text-center">
                                {{ $ru }}
                            </th>
                            @endforeach
                            <th class="text-center">Сумма</th>
                        </thead>
                        <tbody>
                            @foreach($comissions as $monday => $week)
                            <tr>
                                <td class="text-center">
                                    {{ viewdate($monday) }}
                                </td>
                                @foreach($week as $comission)
                                <td class="text-center">
                                    {{ $comission }}
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <div class="card-title">
                    Незагрузившие чек мастера
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <th>Мастер</th>
                        </thead>
                        <tbody>
                            @foreach($masters as $master)
                            <tr>
                                <td>
                                    {{ $master->name }}
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
