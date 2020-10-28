@extends('adminlte::page')

@section('content_header')
<h4>
    Курсы валюты
</h4>
@stop

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-secondary card-outline">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <th>
                                Дата
                            </th>
                            <th class="text-center">
                                Тенге
                            </th>
                            <th class="text-center">
                                Рубль
                            </th>
                            <th class="text-center">
                                Доллар
                            </th>
                        </thead>
                        <tbody>
                            @foreach($currencyRatesGrouped as $date => $currencyRates)
                            <tr>
                                <td>
                                    {{ viewdate($date) }}
                                </td>
                                @foreach($currencyRates as $currencyRate)
                                <td class="text-center">
                                    {{ $currencyRate->rate }}
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $currencyRatesPaginator->links() }}
            </div>
        </div>
    </div>
</div>
@stop
