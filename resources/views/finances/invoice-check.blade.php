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
                    {{ $master['name'] }}
                </div>
            </div>
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-md-4">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div>
                                    <b>
                                        Комиссия за неделю:
                                    </b>
                                    <span class="float-right">
                                @php
                                    $comission = $master['comission'] ?? 0;
                                @endphp
                                        {{ price($comission) }} KZT
                            </span>
                                    @php
                                        if(isset ($comission) && $comission===0){
                                            echo '<span class = "zero-budget" ></span>';
                                        }
                                    @endphp
                                </div>
                                @if (isset($master['currencyRate']) && $master["currencyCode"] != "KZT")
                                    <div class="mt-1">
                                        <b>
                                            В валюте:
                                            <div class="badge badge-warning" title="Валюта за текущую неделю">
                                                {{ $master['currencyRate'] }}
                                            </div>
                                        </b>
                                        <span class="float-right">
                                {{price(round($comission/$master["currencyRate"],2))}} {{ $master["currencyCode"] }}
                            </span>
                                    </div>
                                @endif
                            </li>

                            <li class="list-group-item">
                                <b>
                                    Доп комиссия за неделю:
                                </b>
                                <span class="float-right">
                            {{$unexpectedComission = $master["unexComission"] ?? 0 }} {{ $master["currencyCode"] ?? 'Нет' }}
                        </span>
                            </li>

                            <li class="list-group-item">
                                <b>
                                    Пеня за неделю:
                                    @php
                                    if(isset($master['penalty'])){
                                       $penalty =$master['penalty'] ;
                                    }else{
                                        $penalty = 0;
                                    }
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
                                $masterTotalComission = $comission + $penalty + $unexpectedComission;
                            @endphp
                                    {{ $masterTotalComission }} KZT
                        </span>
                            </li>
                            @php
                            if(isset($master['invoice'])){
                                foreach ($master['invoice'] as $invoice){
                                    //echo "<img src='data:image/png;base64, ".$invoice['file']."' />";
                                }
                            }
                            @endphp

                            <li class="list-group-item text-center">
                                @if (isset($master['invoice']))
                                    <form action="{{ route('invoices.confirm') }}" method="post">
                                        @csrf
                                        @method('PATCH')
                                        <div class="row">
                                            @foreach($master['invoice'] as $invoice)
                                                @if($invoice['file']!="")
                                                <div class="col-4 mb-1">
                                                    <input type="hidden" name="invoices[]" value="{{ $invoice['invoice_id'] }}">
                                                    @if($invoice['confirmed_date'])
                                                        <span class="badge badge-success confirmed">Подтвержден</span>
                                                    @endif
                                                    <img src="data:image/png;base64, {{ $invoice['file'] }}" class="invoice img-fluid" alt="invoice_{{ $invoice['invoice_id'] }}" data-route="{{ route('invoices.destroy', ['invoice' => $invoice['invoice_id']]) }}" />
                                                </div>
                                                @else
                                                    <div class="col-12 mb-1">
                                                        Чеки не загружены
                                                    </div>
                                                    <span class="has-not-invoice"></span>
                                                @endif
                                            @endforeach
                                        </div>

                                        @if(isset($master['invoice']))
                                            <span class="has-invoice"></span>
                                            <div class="form-group">
                                                <button class="btn btn-sm btn-warning">
                                                    Подтвердить
                                                </button>
                                            </div>
                                        @endif

                                    </form>
                                    <button id="invoices-btn" onclick="upload_invoice_func({{ $master['id'] }},{{ $invoice['budget_id'] }})" class="btn btn-sm btn-success" >
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
                        <form action="" method="post" id="fdelete-invoice-form">
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
                $('.master')
                    .filter(':has(span.zero-budget)')
                    .hide();
                $("#filter").show();
                $(".unloaded-invoice-badge").show();
            });

            $("#unconfirmed-invoices-btn").on("click", function(e) {
                e.preventDefault();
                $('.master')
                    .filter(':has(span.confirmed)')
                    .hide();
                $('.master')
                    .filter(':has(span.zero-budget)')
                    .hide();
                $('.master')
                    .filter(':has(span.has-not-invoice)')
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
                if (confirm('Удалить чек ?')) {
                    // Save it!
                    $("#delete-invoice-form").submit();
                }
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
        document.addEventListener('scroll',function (e){
            lastKnowScrollPosition = window.scrollY;
            currenUrl = window.location.href;
            mainUrlArray = currenUrl.split("?position=");
            mainUrlStr = mainUrlArray[0]+"?position="+lastKnowScrollPosition;
            history.pushState({},null,mainUrlStr);
        });
        const queryStr = window.location.search;

        const urlParams = new URLSearchParams(queryStr);
        const pos = urlParams.get("position");
        if (pos){
            window.scrollTo(0,pos);
        }
    </script>
@stop
