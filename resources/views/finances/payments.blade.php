@extends('adminlte::page')

@section('content_header')
<x-week-header header="Выплаты"></x-week-header>
@stop

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <div class="card-title">
                    Выплаты менеджерам
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <th>Менеджер</th>
                            <th>Сумма</th>
                            <th class="text-center">Статус</th>
                        </thead>
                        <tbody>
                            @foreach($managers as $manager)
                            <tr>
                                <td class="align-middle">
                                    {{ $manager->name }}
                                    <span class="badge badge-primary">{{ $manager->premium_rate * 100 }}%</span>
                                </td>
                                <td class="align-middle">
                                    <strong>
                                        {{ price($manager->getBonus(week()->start(), week()->end())) }}
                                    </strong>
                                    KZT
                                </td>
                                <td class="text-center">
                                    @if ($manager->isBonusPaid(week()->start(), week()->end()))
                                    <span class="btn btn-success btn-sm">Оплачено</span>
                                    @else
                                    <form action="{{ route('finances.pay.manager.budgets', ['manager' => $manager]) }}" method="post">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="startDate" value="{{ week()->start() }}">
                                        <input type="hidden" name="endDate" value="{{ week()->end() }}">
                                        <button type="submit" class="btn btn-warning btn-sm">
                                            Оплатить
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <div class="card-title">
                    Выплаты операторам
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <th>Оператор</th>
                            <th>Сумма</th>
                            <th class="text-center">Статус</th>
                        </thead>
                        <tbody>
                            @foreach($operators as $operator)
                            <tr>
                                <td class="align-middle">
                                    {{ $operator->name }}
                                    <span class="badge badge-primary">
                                        {{ $operator->getPoints($operator->getProfit(week()->start(), week()->end())) }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <strong>
                                        {{ price($operator->getProfit(week()->start(), week()->end())) }}
                                    </strong>
                                    KZT
                                </td>
                                <td class="text-center">
                                    @if ($operator->isBonusPaid(week()->start(), week()->end()))
                                    <span class="btn btn-success btn-sm">Оплачено</span>
                                    @else
                                    <form action="{{ route('finances.pay.operator.budgets', ['operator' => $operator]) }}" method="post">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="startDate" value="{{ week()->start() }}">
                                        <input type="hidden" name="endDate" value="{{ week()->end() }}">
                                        <button type="submit" class="btn btn-warning btn-sm">
                                            Оплатить
                                        </button>
                                    </form>
                                    @endif
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
