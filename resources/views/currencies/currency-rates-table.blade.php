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
                <td>
                    {{ viewdate($date) }}
                </td>
                @foreach($currencies as $currency)
                <td class="text-center">
                    {{ $currencyRates->firstWhere("currency_id", $currency->id)->rate ?? "-" }}
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $currencyRatesPaginator->links() }}
