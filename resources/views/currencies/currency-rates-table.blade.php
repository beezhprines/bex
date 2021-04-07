<div class="table-responsive">
    <table class="table table-sm table-striped">
        <thead>
            <th>
                Дата
            </th>
            @foreach($currencies as $currency)
            <th class="text-center">
                {{ $currency->title }}
            </th>
            @endforeach
        </thead>
        <tbody>
            @foreach($currencyRatesGrouped as $date => $currencyRates)
            <tr>
                <form action="{{ route('currenciesRates.update') }}" method="POST">
                    @csrf
                    <td class="text-center">
                        {{ viewdate($date) }}
                        <input hidden name="date" value="{{$date}}">
                    </td>
                    @php
                    $dif = (strtotime(isodate()) - strtotime($date))/(60*60*24);
                    @endphp
                    @foreach($currencies as $currency)

                        @if($dif<=21)
                            <td class="text-center">
                                <input hidden name="currencies[{{ $currency->code }}][code]" value="{{ $currency->code }}" />
                                <input name="currencies[{{ $currency->code }}][value]" value="{{ $currencyRates->firstWhere("currency_id", $currency->id)->rate ?? "-" }}" />
                            </td>
                        @else
                            <td class="text-center">
                                {{ $currencyRates->firstWhere("currency_id", $currency->id)->rate ?? "-" }}
                            </td>
                        @endif

                    @endforeach
                    @if($dif<=21)
                        <td class="text-center">
                            <button type="submit" class="btn btn-success">Сохранить</button>
                        </td>
                    @endif

                </form>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $currencyRatesPaginator->links() }}
