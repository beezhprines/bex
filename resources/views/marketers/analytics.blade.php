@extends('adminlte::page')

@section('content_header')
<x-week-header header="Аналитика"></x-week-header>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <div class="card-title">
                    Расходы команд
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <form action="{{ route('marketers.saveTeamOutcomes') }}" method="POST">
                        @csrf
                        <input type="hidden" name="date" value="{{ week()->last() }}">
                        <table class="table table-sm table-striped">
                            <thead>
                                <th>Команда</th>
                                <th class="text-center">Instagram</th>
                                <th class="text-center">Vkontakte</th>
                            </thead>
                            <tbody>
                                @foreach($teams->orderBy("city_id") as $team)
                                <tr>
                                    <td class="align-middle">
                                        {{ $team->title }}
                                        <input type="hidden" name="teams[{{ $team->id }}][id]" value="{{ $team->id }}" required>
                                    </td>
                                    <td class="text-center">
                                        <ul class="list-group list-group-flush list-group-horizontal-sm p-0">
                                            <li class="list-group-item p-1 bg-transparent border border-light">
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">USD</span>
                                                    </div>
                                                    <input data-currency="USD" type="text" class="form-control exchange" value="0" required>
                                                </div>
                                            </li>
                                            <li class="list-group-item p-1 bg-transparent border border-light">
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">KZT</span>
                                                    </div>
                                                    <input data-currency="KZT" type="text" class="form-control exchange" name="teams[{{ $team->id }}][instagram]" value="{{ $instagram->firstWhere('team_id', $team->id)['amount'] ?? 0 }}" required>
                                                </div>
                                            </li>
                                        </ul>
                                    </td>
                                    <td class="text-center">
                                        <ul class="list-group list-group-flush list-group-horizontal-sm p-0">
                                            <li class="list-group-item p-1 bg-transparent border border-light">
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">RUB</span>
                                                    </div>
                                                    <input data-currency="RUB" type="text" class="form-control exchange" value="0" required>
                                                </div>
                                            </li>
                                            <li class="list-group-item p-1 bg-transparent border border-light">
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">KZT</span>
                                                    </div>
                                                    <input data-currency="KZT" type="text" class="form-control exchange" name="teams[{{ $team->id }}][vk]" value="{{ $vk->firstWhere('team_id', $team->id)['amount'] ?? 0 }}" required>
                                                </div>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="form-group px-4">
                            <input type="submit" class="btn btn-sm btn-warning" value="{{ __('common.save') }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-secondary card-outline">
            <div class="card-header">
                <div class="card-title">
                    Итого
                </div>
            </div>
            <div class="card-body py-0 px-1">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            Расходы мастеров Instagram
                        </span>
                        <strong>
                            {{
                                $instagram->sum(function($item){
                                    return $item["amount"];
                                })
                            }}
                        </strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            Расходы мастеров Vkontakte
                        </span>
                        <strong>
                            {{
                                $vk->sum(function($item){
                                    return $item["amount"];
                                })
                            }}
                        </strong>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card card-secondary card-outline">
            <div class="card-header">
                <div class="card-title">
                    Курсы валют на {{ viewdate(week()->last()) }}
                </div>
            </div>
            <div class="card-body py-0 px-1">
                <ul class="list-group list-group-flush">
                    @foreach($currencies as $currency)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            {{ $currency->title }}
                        </span>
                        <strong>
                            {{ $currencyRates[$currency->code]->rate ?? "-"}}
                        </strong>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    (function($) {
        let currencyRates = JSON.parse('{!! $currencyRates->toJson() !!}');

        let exchange = function(input) {
            let outcome = input.closest("td"),
                value = input.val().length > 0 ? parseFloat(input.val()) : 0;

            outcome.find(".exchange").each(function(i, el) {
                if ($(el).attr("data-currency") != input.attr("data-currency")) {
                    if ($(el).attr("data-currency") === "KZT") {
                        $(el).val(Math.round((value * currencyRates[input.attr("data-currency")].rate + Number.EPSILON) * 100) / 100);
                    } else {
                        $(el).val(Math.round((value / currencyRates[$(el).attr("data-currency")].rate + Number.EPSILON) * 100) / 100);
                    }
                }
            });
        };

        $(document).find(".exchange").each(function(i, el) {
            if ($(el).attr("data-currency") === "KZT") {
                exchange($(el));
            }
        });

        $(".exchange").on("change", function() {
            exchange($(this));
        });
    })(jQuery);
</script>
@stop
