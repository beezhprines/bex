@extends('adminlte::page')

@section('content_header')
<x-week-header header="Мастера"></x-week-header>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <div class="card-title">
                    Дополнительные комиссии мастеров за неделю
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <form action="{{ route('masters.update.comissions') }}" method="post">
                        @csrf
                        @method("PUT")
                        <table class="table table-sm table-striped">
                            <thead>
                                <th>
                                    Мастер
                                </th>
                                <th>
                                    Комиссия
                                </th>
                            </thead>
                            <tbody>
                                @foreach($masters as $master)
                                <tr>
                                    <td class="align-middle">
                                        {{ $master->name }}
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="comissions[{{$master->id}}]" placeholder="Комиссия в тенге" required value="{{ $master->getUnexpectedComission(week()->start(), week()->end()) }}" />
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="form-group text-right px-4">
                            <input type="hidden" name="startDate" value="{{ week()->start() }}" />
                            <input type="hidden" name="endDate" value="{{ week()->end() }}" />
                            <button type="submit" class="btn btn-warning btn-sm">
                                Сохранить
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="alert alert-warning" role="alert">
            <h4 class="alert-heading">Внимание!</h4>
            <p>
                Внимание! Необходимо заново пересчитать текущую неделю, чтобы обновились бонусы!
            </p>
            <x-sync></x-sync>
        </div>
    </div>
</div>
@stop
