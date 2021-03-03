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
                    <form id="team-outcomes-form" action="{{ route('marketers.saveTeamOutcomes') }}" method="POST">
                        @csrf
                        <input type="hidden" name="date" value="{{ week()->last() }}">
                        <table class="table table-sm table-striped">
                            <thead>
                                <th>Команда</th>
                                <th class="text-center">Instagram</th>
                                <th class="text-center">Vkontakte</th>
                            </thead>
                            <tbody>
                                @foreach($teams->sortBy("city_id") as $team)
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
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="position-fixed" style="min-width:300px">
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
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <div class="card-title">
                            Расходы Маркетолога на текущую неделю
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <form action="{{ route('marketers.updateMarketerCustomOutcomes') }}" method="post">
                            @csrf
                            <input type="hidden" name="budget_id" value="{{$budgetMarketerUnOut->id}}">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                    <th>Название</th>
                                    <th>Количество</th>
                                    <th class="text-center"><i class="fa fa-trash"></i></th>
                                    </thead>
                                    <tbody>
                                    @if (empty(json_decode($budgetMarketerUnOut->json, true)))
                                        <tr data-index="0">
                                            <td>
                                                <input type="text" class="form-control form-control-sm" name="custom-outcomes[0][title]" value="" required />
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm" name="custom-outcomes[0][amount]" value="" required />
                                            </td>
                                            <td class="align-middle text-center" title="Удалить">
                                                <i class="fa fa-trash text-danger delete-outcome"></i>
                                            </td>
                                        </tr>
                                    @else
                                        @foreach(json_decode($budgetMarketerUnOut->json, true) as $key => $outcome)
                                            <tr data-index="{{$key + 1}}">
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="custom-outcomes[{{ $key + 1 }}][title]" value="{{ $outcome['title'] }}" required />
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="custom-outcomes[{{ $key + 1 }}][amount]" value="{{ $outcome['amount'] }}" required />
                                                </td>
                                                <td class="align-middle text-center" title="Удалить">
                                                    <i class="fa fa-trash text-danger delete-outcome"></i>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    <tr>
                                        <td colspan="3" class="text-center">
                                            <a href="#" class="add-outcome">
                                                Добавить
                                            </a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group text-right py-2 px-4">
                                <button type="submit" class="btn btn-sm btn-warning">Сохранить</button>
                            </div>
                        </form>
                    </div>
                </div>
            <div class="card card-secondary card-outline">
                <div class="card-header">
                    <div class="card-title">
                        Курсы валют на {{ viewdate($currencyRateDate) }}
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
            <div class="card">
                <div class="card-body text-center p-0">
                    <button id="team-outcomes-submit" class="btn btn-warning btn-block">{{ __('common.save') }}</button>
                </div>
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
                if ($(el).attr("data-currency") != input.attr("data-currency") && currencyRates[input.attr("data-currency")]) {
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
        $("#team-outcomes-submit").on("click", function() {
            $("#team-outcomes-form").submit();
        });

        $(".delete-outcome").on("click", function(e) {
            e.preventDefault();
            $(this).closest("tr").remove();
        });
        $(".add-outcome").on("click", function(e) {
            e.preventDefault();
            let tr = $(this).closest("tbody").find("tr").first().clone(true);
            var lastIndex = $(this).closest("tbody").find("tr").last().prev().attr('data-index');
            lastIndex = parseInt(lastIndex) + 1;
            tr.attr('data-index', lastIndex);
            tr.find('input').each(function(i, e) {
                $(e).val("");
            });
            tr.find("input").first().attr("name", "custom-outcomes[" + lastIndex + "][title]");
            tr.find("input").last().attr("name", "custom-outcomes[" + lastIndex + "][amount]");
            $(this).closest("tr").before(tr);
        });
    })(jQuery);
</script>
@stop
