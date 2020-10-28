@extends('adminlte::page')

@section('content_header')
<h4>
    Комиссии
</h4>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        @forelse($masters as $master)
        <div class="card card-outline card-secondary" id="{{ $master->id }}" data-name="{{ $master->name }}">
            <div class="card-header">
                <div class="card-title">
                    {{ $master->name }}
                    <small class="text-muted">
                        ({{ $master->specialization }})
                    </small>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <th>
                                Услуга
                            </th>
                            <th style="width:20%">
                                Цена
                                <small class="text-muted">{{ $master->currency()->code ?? null }}</small>
                            </th>
                            <th style="width:20%">
                                Комиссия
                                <small class="text-muted">{{ $master->currency()->code ?? null }}</small>
                            </th>
                        </thead>
                        <tbody>
                            @forelse($master->services as $service)
                            <tr>
                                <td>{{ $service->title }}</td>
                                <td>
                                    {{ price($service->price) }}
                                </td>
                                <td>
                                    {{ price($service->comission) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4">@lang('common.no-data')</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @empty
        @lang('common.no-data')
        @endforelse
    </div>
    <div class="col-md-4">
        <div class="card card-outline card-warning position-fixed" style="width:20rem;">
            <div class="card-body">
                <div class="form-group">
                    <label for="search-master">Найти мастера</label>
                    <input type="text" name="search-master" class="form-control" id="search-master" aria-describedby="search-master-help" placeholder="Введите имя...">
                </div>
                <div class="form-group text-center">
                    <button class="btn btn-sm btn-warning" id="search-master-button">
                        Найти
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        var masters = JSON.parse('{!! $masters->map->only(["id", "name"])->toJson() !!}');
        var currentPage = '{{ route("managers.comissions") }}';
        var needle = null;
        var search = null;
        var searchIndex = 0;

        $('#search-master').keypress(function(e) {
            if (e.which == 13) {
                $('#search-master-button').click();
                return false;
            }
        });

        $("#search-master-button").on('click', function() {
            var value = $('#search-master').val().trim();

            if (value !== needle) {
                needle = value;
                search = masters.filter(x => x.name.toLowerCase().includes(needle.toLowerCase()));
                searchIndex = 0;
            }
            if (search.length != 0) {
                if (search[searchIndex]) {
                    window.location.href = `${currentPage}#${search[searchIndex].id}`;

                    $("#search-master").focus();
                    searchIndex++;
                } else {
                    searchIndex = 0;
                }
            }
        });
    });
</script>
@stop
