@extends('adminlte::page')

@section('content_header')
<x-week-header header="Валюты"></x-week-header>
@stop

@section('content')
<div class="row">
    <div class="col-md-6">
        <form action="{{ route('currencies.update.all') }}" method="POST">
            @csrf
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <div class="card-title">
                        Редактирование
                    </div>
                    <div class="card-tools">
                        <div class="btn-group dropleft">
                            <button type="button" class="btn btn-tool btn-transparent btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu">
                                <li>
                                    <a href="#" class="dropdown-item" data-toggle="modal" data-target="#addCurrencyModal">
                                        Добавить
                                    </a>
                                </li>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <th>
                                    Название
                                </th>
                                <th>
                                    Код валюты
                                </th>
                            </thead>
                            <tbody>
                                @foreach($currencies as $currency)
                                <tr>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="currencies[{{ $currency->id }}][title]" value="{{ $currency->title }}" required>
                                    </td>
                                    <td class="align-middle">
                                        <input type="text" class="form-control form-control-sm" name="currencies[{{ $currency->id }}][code]" value="{{ $currency->code }}" required>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-sm btn-warning">Сохранить</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-12">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <div class="card-title">
                    Валюты
                </div>
            </div>
            <div class="card-body p-0">
                @include("currencies.currency-rates-table", [
                "currencies" => $currencies,
                "currencyRatesGrouped" => $currencyRatesGrouped,
                "currencyRatesPaginator" => $currencyRatesPaginator
                ])
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="addCurrencyModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="addCurrencyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCurrencyModalLabel">Добавить новую валюту</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('currencies.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="title">
                            Название
                        </label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" required>
                        @error('title')
                        <span class="error invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="code">
                            Уникальный код валюты
                        </label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" required>
                        @error('code')
                        <span class="error invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-group text-right">
                        <input type="submit" class="btn btn-sm btn-warning" value="{{ __('common.save') }}">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
