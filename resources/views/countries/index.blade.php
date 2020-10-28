@extends('adminlte::page')

@section('content_header')
<h4>
    Страны
</h4>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <form action="{{ route('countries.update.all') }}" method="POST">
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
                                    <a href="#" class="dropdown-item" data-toggle="modal" data-target="#addCountryModal">
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
                                    Код города
                                </th>
                                <th>
                                    Страна
                                </th>
                            </thead>
                            <tbody>
                                @foreach($countries as $country)
                                <tr>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="countries[{{ $country->id }}][title]" value="{{ $country->title }}" required>
                                    </td>
                                    <td class="align-middle">
                                        <input type="text" class="form-control form-control-sm" name="countries[{{ $country->id }}][code]" value="{{ $country->code }}" required>
                                    </td>
                                    <td>
                                        <select class="form-control selectpicker" name="countries[{{ $country->id }}][currency_id]" data-live-search="true" data-size="10" required>
                                            @if(empty($country->currency->id))
                                            <option>
                                                @lang('common.not-selected')
                                            </option>
                                            @endif
                                            @forelse($currencies as $currency)
                                            <option value="{{ $currency->id }}" @if ( $currency->id == $country->currency_id) selected @endif>
                                                {{ $currency->title }}
                                            </option>
                                            @empty
                                            <option>
                                                @lang('common.no-data')
                                            </option>
                                            @endforelse
                                        </select>
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
</div>
<div class="modal fade" id="addCountryModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="addCountryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCountryModalLabel">Добавить новую команду</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('countries.store') }}" method="POST">
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
                            Уникальный код страны
                        </label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" required>
                        @error('code')
                        <span class="error invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="currency_id">
                            Валюта
                        </label>
                        <select class="form-control selectpicker @error('currency_id') is-invalid @enderror" id="currency_id" name="currency_id" data-live-search="true" data-size="10" required>
                            <option>
                                @lang('common.not-selected')
                            </option>
                            @forelse($currencies as $currency)
                            <option value="{{ $currency->id }}">
                                {{ $currency->title }}
                            </option>
                            @empty
                            <option>
                                @lang('common.no-data')
                            </option>
                            @endforelse
                        </select>

                        @error('currency_id')
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
