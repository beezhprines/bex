@extends('adminlte::page')

@section('content_header')
<x-week-header header="Менеджеры">
    <div class="btn-group dropleft">
        <button type="button" class="btn btn-tool btn-transparent btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#create-modal">
                    Добавить
                </a>
            </li>
        </div>
    </div>
</x-week-header>
@stop

@section('content')
<div class="modal fade" id="create-modal" tabindex="-1" aria-labelledby="create-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-modal-label">
                    Добавить менеджера
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('managers.manager-form', [
                'route' => route('managers.store'),
                'method' => 'POST'
                ])
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-8">
        @foreach($managers as $manager)
        <div class="card card-secondary card-outline">
            <div class="card-header">
                <div class="card-title">
                    {{ $manager->name }}
                </div>
                <div class="card-tools">
                    <div class="btn-group dropleft">
                        <button type="button" class="btn btn-tool btn-transparent btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('{{$manager->id}}-auth-form').submit();">
                                    Войти в учетку
                                </a>
                                <form id="{{$manager->id}}-auth-form" action="{{ route('managers.auth', ['manager' => $manager]) }}" method="post">
                                    @csrf
                                    @method("PUT")
                                </form>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('{{$manager->id}}-form').submit();">
                                    Удалить
                                </a>
                                <form id="{{$manager->id}}-form" action="{{ route('managers.destroy', ['manager' => $manager->id]) }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </li>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @include('managers.manager-form', [
                'route' => route('managers.update', ['manager' => $manager]),
                'method' => 'PUT'
                ])
            </div>
        </div>
        @endforeach
    </div>
    <div class="col-md-4">
        <div class="card card-outline card-warning">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            Остаток бонусов
                        </span>
                        <strong>
                            <span id="saldo_bonus">100</span>
                        </strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    let sumjq = function(selector) {
        var sum = 0;
        $(selector).each(function() {
            sum += Number($(this).val());
        });
        return sum;
    }

    let updateSaldo = function() {
        $("#saldo_bonus").html(100 - sumjq("input[name='premium_rate']"));
    }

    $(document).ready(function() {
        updateSaldo();

        $("input[name='premium_rate']").on("change paste keyup", function() {
            updateSaldo();
        });
    });
</script>
@stop
