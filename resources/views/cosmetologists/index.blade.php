@extends('adminlte::page')

@section('content_header')
<x-week-header header="Косметологи">
    <div class="btn-group dropleft">
        <button type="button" class="btn btn-tool btn-transparent btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('all-load-form').submit();">
                    Обновить всех
                </a>
                <form id="all-load-form" action="{{ route('cosmetologists.load.all') }}" method="POST">
                    @csrf
                    @method('PUT')
                </form>
            </li>
        </div>
    </div>
</x-week-header>
@stop

@section('content')
@foreach($cosmetologists as $cosmetologist)
<div class="row">
    <div class="col-md-6">
        <div class="card card-secondary card-outline">
            <div class="card-header">
                <div class="card-title">
                    {{ $cosmetologist->name }} -
                    <small>{{ $cosmetologist->specialization }}</small>
                </div>
                <div class="card-tools">
                    <div class="btn-group dropleft">
                        <button type="button" class="btn btn-tool btn-transparent btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('{{$cosmetologist->id}}-load-form').submit();">
                                    Обновить
                                </a>
                                <form id="{{$cosmetologist->id}}-load-form" action="{{ route('cosmetologists.load', ['cosmetologist' => $cosmetologist]) }}" method="post">
                                    @csrf
                                    @method("PUT")
                                </form>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('{{$cosmetologist->id}}-auth-form').submit();">
                                    Войти в учетку
                                </a>
                                <form id="{{$cosmetologist->id}}-auth-form" action="{{ route('cosmetologists.auth', ['cosmetologist' => $cosmetologist]) }}" method="post">
                                    @csrf
                                    @method("PUT")
                                </form>
                            </li>
                        </div>
                    </div>
                </div>
            </div>
            <form action="{{ route('cosmetologists.update', ['cosmetologist' => $cosmetologist]) }}" method="POST">
                @csrf
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-md-8 px-4 py-2">

                            @include('users.user-form', ['user' => $cosmetologist->user])

                            <div class="form-group">
                                <label>Команда</label>
                                <select class="form-control selectpicker" name="team_id" data-live-search="true" data-size="10" required>
                                    @if(empty($team->team_id))
                                    <option>
                                        @lang('common.not-selected')
                                    </option>
                                    @endif
                                    @forelse($teams as $team)
                                    <option value="{{ $team->id }}" @if ( $team->id == $cosmetologist->team_id) selected @endif>
                                        {{ $team->title }}
                                    </option>
                                    @empty
                                    <option>
                                        @lang('common.no-data')
                                    </option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <img src="{{ $cosmetologist->avatar }}" class="rounded mx-auto d-block mt-4" alt="{{ $cosmetologist->name }}">
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-warning btn-sm">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@stop
