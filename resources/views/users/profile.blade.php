@extends('adminlte::page')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-secondary card-outline">
            <div class="card-header">
                <div class="card-title">
                    Моя учетная запись
                </div>
            </div>
            <form action="{{ route('users.update', ['user' => $user]) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="card-body">
                    @include('users.user-form', ['user' => $user])
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-warning btn-sm">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
