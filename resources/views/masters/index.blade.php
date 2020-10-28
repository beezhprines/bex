@extends('adminlte::page')

@section('content_header')
<h4>
    Мастера

    <span class="float-right">
        <div class="btn-group dropleft">
            <button type="button" class="btn btn-tool btn-transparent btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-ellipsis-v"></i>
            </button>
            <div class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('all-load-form').submit();">
                        Обновить всех
                    </a>
                    <form id="all-load-form" action="{{ route('masters.load.all') }}" method="POST">
                        @csrf
                        @method('PUT')
                    </form>
                </li>
            </div>
        </div>
    </span>
</h4>
@stop

@section('content')
@foreach($masters as $master)
<div class="card card-secondary card-outline">
    <div class="card-header">
        <div class="card-title">
            {{ $master->name }} -
            <small>{{ $master->specialization }}</small>
        </div>
        <div class="card-tools">
            <div class="btn-group dropleft">
                <button type="button" class="btn btn-tool btn-transparent btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('{{$master->id}}-load-form').submit();">
                            Обновить
                        </a>
                        <form id="{{$master->id}}-load-form" action="{{ route('masters.load', ['master' => $master]) }}" method="post">
                            @csrf
                            @method("PUT")
                        </form>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('{{$master->id}}-auth-form').submit();">
                            Войти в учетку
                        </a>
                        <form id="{{$master->id}}-auth-form" action="{{ route('masters.auth', ['master' => $master]) }}" method="post">
                            @csrf
                            @method("PUT")
                        </form>
                    </li>
                    <li>
                        <a data-route="{{ route('services.store', ['master' => $master]) }}" class="dropdown-item add-service" href="#" data-toggle="modal" data-target="#addServiceModal">
                            Добавить услугу
                        </a>
                    </li>
                </div>
            </div>
        </div>
    </div>
    <form action="{{ route('masters.update', ['master' => $master]) }}" method="POST">
        @csrf

        <div class="card-body p-0">
            <div class="row">
                <div class="col-md-4 px-4 py-2">

                    @include('users.user-form', ['user' => $master->user])

                    <div class="form-group">
                        <label>Команда</label>
                        <select class="form-control selectpicker" name="team_id" data-live-search="true" data-size="10" required>
                            @if(empty($team->team_id))
                            <option>
                                @lang('common.not-selected')
                            </option>
                            @endif
                            @forelse($teams as $team)
                            <option value="{{ $team->id }}" @if ( $team->id == $master->team_id) selected @endif>
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
                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <th>
                                    Услуга
                                </th>
                                <th>
                                    Цена
                                </th>
                                <th>
                                    Комиссия
                                </th>
                                <th class="text-center">
                                    Конверсия
                                </th>
                                <th class="text-center">
                                    Длительность
                                </th>
                            </thead>
                            <tbody>
                                @foreach($master->services as $service)
                                <tr>
                                    <td class="align-middle @if($service->conversion) text-bold @endif">
                                        {{ $service->title }}
                                    </td>
                                    <td class="align-middle">
                                        {{ price($service->price) }}
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="services[{{$service->id}}][comission]" value="{{ $service->comission }}" />
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="custom-control custom-switch">
                                            <input type="hidden" name="services[{{$service->id}}][conversion]" value="{{ optional($service)->conversion ?? 0 }}">
                                            <input type="checkbox" id="conversion_{{$service->id}}" class="custom-control-input" {{ optional($service)->conversion ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="conversion_{{$service->id}}">
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ secondsToTime(intval($service->seance_length)) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <button type="submit" class="btn btn-warning btn-sm">Сохранить</button>
        </div>
    </form>
</div>
@endforeach

<div class="modal fade" id="addServiceModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="addServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addServiceModalLabel">Добавить новую услугу</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="title">Название услуги</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="seance_length">Длительность</label>
                                <select name="seance_length" id="seance_length" class="form-control">
                                    <option value="900">00:15</option>
                                    <option value="1800">00:30</option>
                                    <option value="2400">00:45</option>
                                    <option value="3600">01:00</option>
                                    <option value="4500">01:15</option>
                                    <option value="5400">01:30</option>
                                    <option value="6300">01:45</option>
                                    <option value="7200">02:00</option>
                                    <option value="8100">02:15</option>
                                    <option value="9000">02:30</option>
                                    <option value="9900">02:45</option>
                                    <option value="10800">03:00</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price">Цена</label>
                                <input type="text" class="form-control" name="price" id="price" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="comission">Комиссия</label>
                                <input type="text" class="form-control" name="comission" id="comission" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="hidden" name="conversion" value="0">
                                    <input type="checkbox" class="custom-control-input" id="conversion">
                                    <label class="custom-control-label" for="conversion">
                                        Конверсия
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-sm btn-warning">
                                    Добавить
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $("#addServiceModal").on("show.bs.modal", function(e) {
            $(this).find("form").attr("action", $(e.relatedTarget).attr("data-route"));
        })
    });
</script>
@stop
