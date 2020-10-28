@extends('adminlte::page')

@section('content_header')
<h4>
    Операторы
    <span class="float-right">
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
    </span>
</h4>
@stop

@section('content')
<div class="modal fade" id="create-modal" tabindex="-1" aria-labelledby="create-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-modal-label">
                    Добавить оператора
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('operators.operator-form', ['route' => route('operators.store'), 'method' => 'POST'])
            </div>
        </div>
    </div>
</div>
@foreach($operators as $operator)
<div class="card card-secondary card-outline">
    <div class="card-header">
        <div class="card-title">
            {{ $operator->name }}
        </div>
        <div class="card-tools">
            <div class="btn-group dropleft">
                <button type="button" class="btn btn-tool btn-transparent btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('{{$operator->id}}-auth-form').submit();">
                            Войти в учетку
                        </a>
                        <form id="{{$operator->id}}-auth-form" action="{{ route('operators.auth', ['operator' => $operator]) }}" method="post">
                            @csrf
                            @method("PUT")
                        </form>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('{{$operator->id}}-form').submit();">
                            Удалить
                        </a>
                        <form id="{{$operator->id}}-form" action="{{ route('operators.destroy', ['operator' => $operator->id]) }}" method="post">
                            @csrf
                            @method('DELETE')
                        </form>
                    </li>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="row">
            <div class="col-md-4 px-4 py-2">
                @include('operators.operator-form', [
                'operator' => $operator,
                'route' => route('operators.update', [
                'operator' => $operator
                ]),
                'method' => 'PUT'
                ])
            </div>
            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <th>
                                Команды
                            </th>
                            <th>
                                Мастера
                            </th>
                        </thead>
                        <tbody>
                            @foreach($operator->teams as $team)
                            <tr>
                                <td>
                                    {{ $team->title }}
                                </td>
                                <td>
                                    @foreach($team->masters as $master)
                                    {{ $master->name }},
                                    @endforeach
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

@stop
