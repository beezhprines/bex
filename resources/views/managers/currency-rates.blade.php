@extends('adminlte::page')

@section('content_header')
<x-week-header header="Курсы валюты"></x-week-header>
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
