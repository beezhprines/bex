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
