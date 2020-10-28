@extends('adminlte::page')

@section('content_header')
<h4>
    Курсы валюты
</h4>
@stop

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-secondary card-outline">
            <div class="card-body p-0">
                @include("currencies.currency-rates-table", [
                "currencyRatesGrouped" => $currencyRatesGrouped,
                "currencyRatesPaginator" => $currencyRatesPaginator
                ])
            </div>
        </div>
    </div>
</div>
@stop
