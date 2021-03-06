@extends('adminlte::page')

@section('content_header')
<x-week-header header="Услуги">
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
</x-week-header>
@stop

@section('content')
@foreach($masters as $master)
<div id="master-{{ $master->id }}" class="card card-secondary card-outline id-scrollable">
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
                        <a data-route="{{ route('services.store', ['master' => $master]) }}" class="dropdown-item add-service" href="#" data-toggle="modal" data-target="#addServiceModal">
                            Добавить услугу
                        </a>
                    </li>
                </div>
            </div>
        </div>
    </div>
    <form action="{{ route('masters.services.update', ['master' => $master]) }}" method="POST">
        @csrf

        <div class="card-body p-0">
            @include("services.services-table-form", ["services" => $master->services])
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
