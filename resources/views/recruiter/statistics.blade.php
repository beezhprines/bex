@extends('adminlte::page')

@section('content_header')
    <x-week-header header="Статистика">
        <div class="btn-group dropleft">
            <button type="button" class="btn btn-tool btn-transparent btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-ellipsis-v"></i>
            </button>
            <div class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="#" id="unloaded-invoices-btn">
                        Должники
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" id="unconfirmed-invoices-btn">
                        Не подтвержденные
                    </a>
                </li>
            </div>
        </div>
    </x-week-header>
@stop

@section('content')

    @foreach($masters as $master)
        <div id="filter" class="mb-2" style="display: none;">
            <span class="badge badge-warning unloaded-invoice-badge" style="display: none;">Должники</span>
            <span class="badge badge-warning unconfirmed-invoice-badge" style="display: none;">Не подтвержденные</span>
            <a href="#" class="badge badge-primary clear-filter-btn">Очистить</a>
        </div>
        <div class="card card-outline card-secondary master">
            <div class="card-header">
                <div class="card-title">
                    {{ $master->name }}
                </div>
            </div>
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-md-8">
                        @if (!empty($master->services))
                            @include('recruiter.week-price-services', ['master' => $master])
                        @else
                            @lang("common.no-data")
                        @endif
                    </div>
                    <div class="col-md-4">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div>
                                    <b>
                                        Комиссия за неделю:
                                    </b>
                                    <span class="float-right">
                                @php
                                    $comission = $master->getComission(week()->start(), week()->end());
                                @endphp
                                        {{ price($comission) }} KZT
                            </span>
                                </div>
                                @if (! empty($master->currency()) && $master->currency()->code != "KZT")
                                    <div class="mt-1">
                                        @php
                                            $avgRate = round($master->currency()->avgRate(week()->start(), week()->end()), 2);
                                        @endphp
                                        <b>
                                            В валюте:
                                            <div class="badge badge-warning" title="Средняя валюта за текущую неделю">
                                                {{ $avgRate }}
                                            </div>
                                        </b>
                                        <span class="float-right">
                                {{ price($master->getComissionWithoutExchange(week()->start(), week()->end())) }} {{ $master->currency()->code }}
                            </span>
                                    </div>
                                @endif
                            </li>

                            @php
                                $unexpectedComission = $master->getUnexpectedComission(week()->start(), week()->end(), true);
                            @endphp
                            <li class="list-group-item">
                                <b>
                                    Доп комиссия за неделю:
                                </b>
                                <span class="float-right">
                            {{ price($unexpectedComission) }} {{ $master->currency()->code ?? 'Нет' }}
                        </span>
                            </li>

                            <li class="list-group-item">
                                <b>
                                    Пеня за неделю:
                                    @php
                                        $penalty = $master->getPenalty(week()->start(), week()->end());
                                    @endphp
                                    <div class="badge badge-warning">
                                        @if ($comission == 0)
                                            0 %
                                        @else
                                            {{ round(($penalty / $comission) * 100)}} %
                                        @endif
                                    </div>
                                </b>
                                <span class="float-right">
                            {{ price($penalty) }} KZT
                        </span>
                            </li>

                            <li class="list-group-item">
                                <b>
                                    Итого:
                                </b>
                                <span class="float-right">
                            @php
                                $unexpectedComission = $master->getUnexpectedComission(week()->start(), week()->end());
                                $masterTotalComission = $comission + $penalty + $unexpectedComission;
                            @endphp
                                    {{ $masterTotalComission }} KZT
                        </span>
                            </li>
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
                                    $budget = $master->getBudget(week()->end(), $masterComissionBudgetType->id);
                                @endphp
                                @if (!empty($budget))
                                    <form action="{{ route('invoices.confirm') }}" method="post">
                                        @csrf
                                        @method('PATCH')
                                        <div class="row">
                                            @forelse($budget->invoices as $invoice)
                                                <div class="col-4 mb-1">
                                                    <input type="hidden" name="invoices[]" value="{{ $invoice->id }}">
                                                    @if($invoice->confirmed_date)
                                                        <span class="badge badge-success confirmed">Подтвержден</span>
                                                    @endif
                                                    <img src="data:image/png;base64, {{ $invoice->file }}" class="invoice img-fluid" alt="invoice_{{ $invoice->id }}" data-route="{{ route('invoices.destroy', ['invoice' => $invoice]) }}" />
                                                </div>
                                            @empty
                                                <div class="col-12 mb-1">
                                                    Чеки не загружены
                                                </div>
                                            @endforelse
                                        </div>
                                        @if(!$budget->invoices->isEmpty())
                                            <span class="has-invoice"></span>
                                            <div class="form-group">
                                                <button class="btn btn-sm btn-warning">
                                                    Подтвердить
                                                </button>
                                            </div>
                                        @endif
                                    </form>
                                    <button id="invoices-btn" onclick="upload_invoice_func({{ $master->id }},{{ $budget->id }})" class="btn btn-sm btn-success" >
                                        Загрузить чек
                                    </button>
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
    @endforeach
    <div>
        <form action="{{ route('invoices.store.many') }}" method="POST" id="store-invoices-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="master_id" name="master_id" value="0" />
            <input type="hidden" id="budget_id" name="budget_id" value="0" />
            <input type="file"  class="d-none" name="invoices[]" id="invoices" accept="image/*" multiple required>
        </form>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $(".invoice").on("click", function() {
                var modal = $("#invoice-modal");
                modal.find("form").attr("action", $(this).attr('data-route'));
                modal.find(".content").html($(this).clone());
                modal.modal('show');
            });

            $("#unloaded-invoices-btn").on("click", function(e) {
                e.preventDefault();
                $('.master')
                    .filter(':has(span.has-invoice)')
                    .hide();
                $("#filter").show();
                $(".unloaded-invoice-badge").show();
            });

            $("#unconfirmed-invoices-btn").on("click", function(e) {
                e.preventDefault();
                $('.master')
                    .filter(':has(span.confirmed)')
                    .hide();
                $("#filter").show();
                $(".unconfirmed-invoice-badge").show();
            });

            $(".clear-filter-btn").on("click", function(e) {
                e.preventDefault();
                $('.master').show();
                $("#filter .badge-warning").hide();
                $("#filter").hide();
            });
            $("#delete-invoice").on("click", function() {
                $("#delete-invoice-form").submit();
            });

        });

        function upload_invoice_func(master_id,budget_id){
            $("#master_id").value=master_id;
            $("#budget_id").value=budget_id;
            document.getElementById("master_id").value = master_id;
            document.getElementById("budget_id").value = budget_id;

            $("#invoices").click();
        }
        $("#invoices").on('change', function() {
            $("#store-invoices-form").submit();
        });
    </script>
@stop
