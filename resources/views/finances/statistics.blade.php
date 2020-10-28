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
@foreach($masters as $master)
<div class="card card-outline card-secondary">
    <div class="card-header">
        <div class="card-title">
            {{ $master->name }}
        </div>
    </div>
    <div class="card-body p-0">
        <div class="row">
            <div class="col-md-8">
                @if (!empty($master->services))
                @include('masters.week-price-services', ['master' => $master])
                @else
                @lang("common.no-data")
                @endif
            </div>
            <div class="col-md-4">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <b>
                            Комиссия за неделю:
                        </b>
                        <span class="float-right">
                            @php
                            $comission = $master->getComission(week()->start(), week()->end());
                            @endphp
                            {{ price($comission) }} KZT
                        </span>
                    </li>
                    @if (! empty($master->currency()) && $master->currency()->code != "KZT")
                    <li class="list-group-item">
                        <b>
                            Комиссия за неделю <small class="text-muted" title="Средний курс за неделю">({{ $master->currency()->avgRate(week()->start(), week()->end()) }})</small>
                        </b>
                        <span class="float-right">
                            {{ price($comission / $master->currency()->avgRate(week()->start(), week()->end())) }} {{ $master->currency()->code }}
                        </span>
                    </li>
                    @endif
                    <li class="list-group-item">
                        <b>Прибыль мастера за неделю:</b>
                        <span class="float-right">
                            @php
                            $profit = $master->getProfit(week()->start(), week()->end());
                            @endphp
                            {{ price($profit) }} KZT
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>Сумма за неделю:</b>
                        <span class="float-right">
                            {{ price($comission + $profit) }} KZT
                        </span>
                    </li>
                    <li class="list-group-item text-center">
                        @php
                        $budget = $master->getBudget(week()->end(), $budgetType->id);
                        @endphp
                        @if (!empty($budget))
                        <div class="row">
                            @forelse($budget->invoices as $invoice)
                            <div class="col-4 mb-1">
                                <img src="data:image/png;base64, {{ $invoice->file }}" class="invoice img-fluid" alt="invoice_{{ $invoice->id }}" />
                            </div>
                            @empty
                            <div class="col-12 mb-1">
                                Чеки не загружены
                            </div>
                            @endforelse
                        </div>
                        @else
                        <span class="text-danger">
                            Не создан бюджет на дату {{ week()->end() }}
                        </span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="invoice-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="content text-center"></div>
                <hr>
                <div class="text-right">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
@stop

@section('js')
<script>
    $(document).ready(function() {
        $(".invoice").on("click", function() {
            var modal = $("#invoice-modal");
            modal.find(".content").html($(this).clone());
            modal.modal('show');
        });
    });
</script>
@stop
