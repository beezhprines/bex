@extends('adminlte::page')

@section('content_header')
<x-week-header header="Диаграммы чатов"></x-week-header>
@stop

@section('content')
<x-period-control route="route('charts.chats')"></x-period-control>

@foreach($teams as $team)
<div class="card">
    <div class="card-body">
        <div id="chart-team-{{ $team->id }}"></div>
    </div>
</div>
@endforeach
@stop

@section('js')
<script src="https://code.highcharts.com/highcharts.src.js"></script>

<script>
    const chats = JSON.parse('{!! $chats->toJson() !!}');
    const urlParams = new URLSearchParams(window.location.search);

    chats.forEach(chat => {
        Highcharts.chart(`chart-team-${chat.info.team_id}`, {
            chart: {
                type: 'area'
            },
            title: chat.title,
            subtitle: chat.subtitle,
            xAxis: chat.xAxis,
            yAxis: chat.yAxis,
            plotOptions: {
                area: {
                    fillOpacity: 0.5,
                    dataLabels: {
                        enabled: true,
                    }
                }
            },
            series: chat.series
        });
    });
    (function($) {
        $('.date').datepicker({
            format: "yyyy-mm-dd",
            weekStart: 1,
            endDate: moment()
                .isoWeek(moment().isoWeek())
                .format("YYYY-MM-DD"),
            maxViewMode: 1,
            language: "ru",
            multidate: false,
            autoclose: true
        });
    })(jQuery);
</script>
@stop
