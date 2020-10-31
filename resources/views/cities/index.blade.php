@extends('adminlte::page')

@section('content_header')
<x-week-header header="Города"></x-week-header>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <form action="{{ route('cities.update.all') }}" method="POST">
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
                                    <a href="#" class="dropdown-item" data-toggle="modal" data-target="#addCityModal">
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
                                @foreach($cities as $city)
                                <tr>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="cities[{{ $city->id }}][title]" value="{{ $city->title }}" required>
                                    </td>
                                    <td class="align-middle">
                                        <input type="text" class="form-control form-control-sm" name="cities[{{ $city->id }}][code]" value="{{ $city->code }}" required>
                                    </td>
                                    <td>
                                        <select class="form-control selectpicker" name="cities[{{ $city->id }}][country_id]" data-live-search="true" data-size="10" required>
                                            @if(empty($city->country->id))
                                            <option>
                                                @lang('common.not-selected')
                                            </option>
                                            @endif
                                            @forelse($countries as $country)
                                            <option value="{{ $country->id }}" @if ( $country->id == $city->country_id) selected @endif>
                                                {{ $country->title }}
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
<div class="modal fade" id="addCityModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="addCityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCityModalLabel">Добавить новый город</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('cities.store') }}" method="POST">
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
                            Уникальный код города
                        </label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" required>
                        @error('code')
                        <span class="error invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="country_id">
                            Страна
                        </label>
                        <select class="form-control selectpicker @error('country_id') is-invalid @enderror" id="country_id" name="country_id" data-live-search="true" data-size="10" required>
                            <option>
                                @lang('common.not-selected')
                            </option>
                            @forelse($countries as $country)
                            <option value="{{ $country->id }}">
                                {{ $country->title }}
                            </option>
                            @empty
                            <option>
                                @lang('common.no-data')
                            </option>
                            @endforelse
                        </select>

                        @error('country_id')
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
