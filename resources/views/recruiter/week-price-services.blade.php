<div class="table-responsive">
    <table class="table table-sm table-striped">
        <thead>
        <th>Услуга</th>
        <th class="text-center">Записи</th>
        <th class="text-center">Цена</th>
        <th class="text-center">Комиссия</th>
        </thead>
        <tbody>
        @foreach($master->services as $service)
            <tr>
                <td>
                    {{ $service->title }}
                </td>
                <td class="text-center">
                    @php $serviceCount = $service->getRecordsBetweenDates(week()->start(), week()->end())->count(); @endphp
                    {{ $serviceCount }}
                </td>
                <td class="text-center">
                    {{ price($service->price) }}
                </td>
                <td class="text-center">
                    {{ price($service->comission) }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
