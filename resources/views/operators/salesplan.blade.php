@extends('adminlte::page')

@section('content_header')
<x-week-header header="План продаж"></x-week-header>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-warning card-outline">
            <div class="card-body">
                <div class="mb-2">
                    <x-weekplan-progress :profit='$points' :milestones='$milestones'></x-weekplan-progress>
                </div>
                <div>
                    Итого: <strong>{{ $profit }}</strong>
                </div>
            </div>
        </div>
    </div>
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
</div>
@stop
