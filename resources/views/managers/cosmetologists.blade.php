@extends('adminlte::page')

@section('content_header')
<x-week-header header="Косметологи"></x-week-header>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <div class="card-title">
                    Комиссии косметологов за неделю
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <form action="{{ route('cosmetologists.update.comissions') }}" method="post">
                        @csrf
                        @method("PUT")
                        <table class="table table-sm table-striped">
                            <thead>
                                <th>
                                    Косметолог
                                </th>
                                <th>
                                    Комиссия
                                </th>
                            </thead>
                            <tbody>
                                @foreach($cosmetologists as $cosmetologist)
                                <tr>
                                    <td class="align-middle">
                                        {{ $cosmetologist->name }}
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="comissions[{{$cosmetologist->id}}]" placeholder="Комиссия в тенге" required value="{{ $cosmetologist->getComission(week()->start(), week()->end()) }}"/>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="form-group text-right px-4">
                            <input type="hidden" name="startDate" value="{{ week()->start() }}"/>
                            <input type="hidden" name="endDate" value="{{ week()->end() }}"/>
                            <button type="submit" class="btn btn-warning btn-sm">
                                Сохранить
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
