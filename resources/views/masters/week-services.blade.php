<div class="card card-secondary card-outline">
    <div class="card-header">
        <div class="card-title">
            {{ $master->name }}
            <small>({{ $master->specialization }})</small>
        </div>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
        </div>
    </div>
    <div class="card-body p-0">
        @if($master->services->count() > 0)
        <div class="table-responsive">
            <table class="table table-borderless table-sm table-striped">
                <thead>
                    <tr>
                        <th rowspan="2" class="align-middle" style="width:30%;min-width:200px;">
                            Услуга
                        </th>
                        <th colspan="8" class="text-center">
                            Записи
                        </th>
                    </tr>
                    <tr>
                        @foreach(week()->weekTitles() as $en => $ru)
                        <th class="text-center p-1">
                            {{ $ru }}
                        </th>
                        @endforeach
                        <th class="text-center" style="width:20%;min-width:100px;">
                            Итого
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($master->services as $service)
                    <tr class="border-top">
                        <td>
                            <span class="{{ $service->conversion ? 'text-bold' : '' }}" title="{{ $service->conversion ? 'Конверсия' : '' }}">
                                {{ $service->title ?: __('common.no-title') }}
                            </span>
                            @if ($team->operator)
                            <span class="badge badge-warning float-right" title="Баллы оператора {{ $team->operator->name }}">
                                {{ round($team->operator->solvePointsPerService($service, week()->start(), week()->end())) }}
                            </span>
                            @endif
                        </td>
                        @php
                            $recordsWeekCount = 0;
                        @endphp
                        @foreach(week()->range() as $day => $date)
                        <td class="text-center p-1">
                            @php
                                $date = date_format($date, config('app.iso_date'));
                                $recordsCount = $service->getRecordsBetweenDates($date, $date)->count();
                                $recordsWeekCount += $recordsCount;
                            @endphp
                            {{ $recordsCount }}
                        </td>
                        @endforeach
                        <td class="text-center">
                            {{ $recordsWeekCount }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        @lang("common.no-data")
        @endif
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
        <small>
            {{ $master->team->title }}
        </small>
    </div>
</div>
