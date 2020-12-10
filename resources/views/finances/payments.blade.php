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
                                    @php
                                    $isPaid = $manager->isBonusPaid(week()->start(), week()->end());
                                    @endphp
                                    <form action="{{ route('finances.pay.manager.budgets', ['manager' => $manager, 'action' => !$isPaid]) }}" method="post">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="startDate" value="{{ week()->start() }}">
                                        <input type="hidden" name="endDate" value="{{ week()->end() }}">
                                        <button type="submit" class="btn @if ($isPaid) btn-success @else btn-warning   @endif btn-sm">
                                            {{ $isPaid ? 'Оплачено' : 'Оплатить' }}
                                        </button>
                                    </form>
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
                                    @php
                                    $isPaid = $operator->isBonusPaid(week()->start(), week()->end());
                                    @endphp
                                    <form action="{{ route('finances.pay.operator.budgets', ['operator' => $operator, 'action' => !$isPaid]) }}" method="post">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="startDate" value="{{ week()->start() }}">
                                        <input type="hidden" name="endDate" value="{{ week()->end() }}">
                                        <button type="submit" class="btn @if ($isPaid) btn-success @else btn-warning   @endif btn-sm">
                                            {{ $isPaid ? 'Оплачено' : 'Оплатить' }}
                                        </button>
                                    </form>
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
