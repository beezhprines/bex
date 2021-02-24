@extends('adminlte::page')

@section('content_header')
<x-week-header header="Команды"></x-week-header>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <form action="{{ route('teams.update.all') }}" method="POST">
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
                                    <a href="#" class="dropdown-item" data-toggle="modal" data-target="#addTeamModal">
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
                                    Оператор
                                </th>
                                <th>
                                    Город
                                </th>
                                <th>
                                    Коэффициент сложности
                                </th>
                                <th>

                                </th>
                            </thead>
                            <tbody>
                                @foreach($teams as $team)
                                <tr>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="teams[{{ $team->id }}][title]" value="{{ $team->title }}" required>
                                    </td>
                                    <td>
                                        <select class="form-control selectpicker" name="teams[{{ $team->id }}][operator_id]" data-live-search="true" data-size="10" required>
                                            @if(empty($team->operator->id))
                                            <option>
                                                @lang('common.not-selected')
                                            </option>
                                            @endif
                                            @forelse($operators as $operator)
                                            <option value="{{ $operator->id }}" @if ( $operator->id == $team->operator_id) selected @endif>
                                                {{ $operator->name }}
                                            </option>
                                            @empty
                                            <option>
                                                @lang('common.no-data')
                                            </option>
                                            @endforelse
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control selectpicker" name="teams[{{ $team->id }}][city_id]" data-live-search="true" data-size="10" required>
                                            @if(empty($team->city->id))
                                            <option>
                                                @lang('common.not-selected')
                                            </option>
                                            @endif
                                            @forelse($cities as $city)
                                            <option value="{{ $city->id }}" @if ( $city->id == $team->city_id) selected @endif>
                                                {{ $city->title }}
                                            </option>
                                            @empty
                                            <option>
                                                @lang('common.no-data')
                                            </option>
                                            @endforelse
                                        </select>
                                    </td>
                                    <td>
                                    <input type="button" onclick="acrhivate('{{$team->id}}','{{$team->title}}')" class="btn btn-danger" value="Архивировать">
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

<div class="modal fade" id="addTeamModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="addTeamModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTeamModalLabel">Добавить новую команду</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('teams.store') }}" method="POST">
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
                        <label for="premium_rate">
                            Коэффициент сложности
                        </label>
                        <input type="text" class="form-control @error('premium_rate') is-invalid @enderror" id="premium_rate" name="premium_rate" required>
                        @error('premium_rate')
                        <span class="error invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="operator_id">
                            Оператор
                        </label>
                        <select class="form-control selectpicker @error('operator_id') is-invalid @enderror" id="operator_id" name="operator_id" data-live-search="true" data-size="10" required>
                            <option>
                                @lang('common.not-selected')
                            </option>
                            @forelse($operators as $operator)
                            <option value="{{ $operator->id }}">
                                {{ $operator->name }}
                            </option>
                            @empty
                            <option>
                                @lang('common.no-data')
                            </option>
                            @endforelse
                        </select>

                        @error('operator_id')
                        <span class="error invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="city_id">
                            Город
                        </label>
                        <select class="form-control selectpicker @error('city_id') is-invalid @enderror" id="city_id" name="city_id" data-live-search="true" data-size="10" required>
                            <option>
                                @lang('common.not-selected')
                            </option>
                            @forelse($cities as $city)
                            <option value="{{ $city->id }}">
                                {{ $city->title }}
                            </option>
                            @empty
                            <option>
                                @lang('common.no-data')
                            </option>
                            @endforelse
                        </select>

                        @error('city_id')
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
<form id="teamForm" action="{{route('teams.archivate.team')}}" method="POST">
    @csrf
    <input id="team" name="team" type="hidden" value="{{0}}">
</form>
@stop
@section('js')
    <script>
        function acrhivate(id,teamTitle){
            if (confirm('Вы точно хотите архивировать "'+teamTitle+'" ?')) {
                document.getElementById('team').value = id;
                console.log(document.getElementById('team').value);
                document.getElementById('teamForm').submit();
            } else {
                // Do nothing!
                console.log('Thing was not saved to the database.');
            }

        }

    </script>
@stop
