@extends('adminlte::page')

@section('content_header')
<h4>
    Маркетологи
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
                    Добавить маркетолога
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('marketers.marketer-form', [
                'route' => route('marketers.store'),
                'method' => 'POST'
                ])
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        @foreach($marketers as $marketer)
        <div class="card card-secondary card-outline">
            <div class="card-header">
                <div class="card-title">
                    {{ $marketer->name }}
                </div>
                <div class="card-tools">
                    <div class="btn-group dropleft">
                        <button type="button" class="btn btn-tool btn-transparent btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('{{$marketer->id}}-auth-form').submit();">
                                    Войти в учетку
                                </a>
                                <form id="{{$marketer->id}}-auth-form" action="{{ route('marketers.auth', ['marketer' => $marketer]) }}" method="post">
                                    @csrf
                                    @method("PUT")
                                </form>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('{{$marketer->id}}-form').submit();">
                                    Удалить
                                </a>
                                <form id="{{$marketer->id}}-form" action="{{ route('marketers.destroy', ['marketer' => $marketer->id]) }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </li>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @include('marketers.marketer-form', [
                'route' => route('marketers.update', ['marketer' => $marketer]),
                'method' => 'PUT'
                ])
            </div>
        </div>
        @endforeach</div>
</div>
@stop
