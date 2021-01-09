@extends('adminlte::page')

@section('content_header')
<x-week-header header="Статистика"></x-week-header>
@stop

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-outline card-secondary">
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <b>Комиссия за неделю:</b>
                        <span class="float-right">
                            {{ price($comission) }} {{ $currency->code }}
                        </span>
                    </li>
                    @if ($penalty != 0)
                    <li class="list-group-item">
                        <b>
                            Пеня за неделю:
                            <div class="badge badge-warning">
                                {{ round(($penalty / $comission) * 100) }} %
                            </div>
                        </b>
                        <span class="float-right">
                        {{ price($penalty) }} {{ $currency->code }}
                        </span>
                    </li>
                    @endif
                    @if ($unexpectedComission != 0)
                    <li class="list-group-item">
                        <b>
                            Доп комиссия за неделю:
                        </b>
                        <span class="float-right">
                        {{ price($unexpectedComission) }} {{ $currency->code }}
                        </span>
                    </li>
                    @endif
                    @if ($penalty != 0 || $unexpectedComission != 0)
                    <li class="list-group-item">
                        <b>
                            Итого:
                        </b>
                        <span class="float-right">
                        {{ price($penalty + $comission + $unexpectedComission) }} {{ $currency->code }}
                        </span>
                    </li>
                    @endif
                    <li class="list-group-item text-center">
                        @if (!empty($budget))
                        <div class="row">
                            @foreach($budget->invoices as $invoice)
                            <div class="col-4 mb-1">
                                <img src="data:image/png;base64, {{ $invoice->file }}" class="invoice img-fluid" alt="invoice_{{ $invoice->id }}" data-route="{{ route('invoices.destroy', ['invoice' => $invoice]) }}" />
                            </div>
                            @endforeach
                        </div>
                        <div>
                            <form action="{{ route('invoices.store.many') }}" method="POST" id="store-invoices-form" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="master_id" value="{{ $master->id }}" />
                                <input type="hidden" name="budget_id" value="{{ $budget->id }}" />
                                <input type="file" class="d-none" name="invoices[]" id="invoices" accept="image/*" multiple required>
                            </form>
                            <button id="invoices-btn" class="btn btn-sm btn-warning">
                                Загрузить чек
                            </button>
                        </div>
                        @else
                        <span class="text-danger">Не создан бюджет на дату {{ week()->end() }}</span>
                        @endif
                    </li>
                    <li class="list-group-item text-center">
                        Доход (с вычетом комиссий)
                    </li>
                    <li class="list-group-item">
                        <b>Текущий месяц:</b>
                        <span class="float-right">
                            {{ price(round($master->getProfit(date('Y-m-01'), date('Y-m-t'))) / $avgRate) }} {{ $currency->code }}
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>За последние 12 недель:</b>
                        <span class="float-right">
                            {{ price(round($master->getProfit(week()->monday(isodate(strtotime(isodate() . ' -84 day'))), week()->sunday(isodate()))) / $avgRate) }} {{ $currency->code }}
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>За все время:</b>
                        <span class="float-right">
                            {{ price(round($master->getTotalProfit()) / $avgRate) }} {{ $currency->code }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <div class="card-title">
                    {{ $master->name }}
                </div>
            </div>
            <div class="card-body p-0">
                @if (!empty($master->services))
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <th>Услуга</th>
                            <th class="text-center">Записи</th>
                            <th class="text-center">Цена</th>
                            <th class="text-center">Общая сумма</th>
                            <th class="text-center">Комиссия</th>
                        </thead>
                        <tbody>
                            @foreach($master->services as $service)
                            <tr>
                                <td>
                                    {{ $service->title }}
                                </td>
                                <td class="text-center">
                                    @php $serviceCount = $service->getRecordsBetweenDates(week()->start(), week()->end())->count(); @endphp
                                    {{ $serviceCount }}
                                </td>
                                <td class="text-center">
                                    {{ price($service->price) }}
                                </td>
                                <td class="text-center">
                                    {{ price($service->price * $serviceCount) }}
                                </td>
                                <td class="text-center">
                                    {{ price($service->comission * $serviceCount) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="invoice-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <form action="" method="post" id="delete-invoice-form">
                    @csrf
                    @method('DELETE')
                    <div class="content text-center"></div>
                </form>
                <hr>
                <div class="row">
                    <div class="col">
                        <button id="delete-invoice" type="button" class="btn btn-warning">
                            Удалить
                        </button>
                    </div>
                    <div class="col text-right">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Закрыть
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#invoices-btn').on('click', function() {
            $("#invoices").click();
        });

        $("#invoices").on('change', function() {
            $("#store-invoices-form").submit();
        });

        $(".invoice").on("click", function() {
            var modal = $("#invoice-modal");
            modal.find("form").attr("action", $(this).attr('data-route'));
            modal.find(".content").html($(this).clone());
            modal.modal('show');
        });

        $("#delete-invoice").on("click", function() {
            $("#delete-invoice-form").submit();
        });
    });
</script>
@stop
