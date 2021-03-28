@extends('adminlte::page')

@section('content_header')
<x-week-header header="Диаграммы чатов"></x-week-header>
@stop

@section('content')
<x-period-control :route="route('charts.chats')"></x-period-control>

<div class="card">
    <div class="card-body">
        <div id="chart-team-contacts-and-outcomes"></div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div id="chart-team-leads"></div>
    </div>
</div>
@stop

@section('js')
<script src="https://code.highcharts.com/highcharts.src.js"></script>

<script>
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

    const commonContactToChat = {!! json_encode($commonContactToChat) !!};
    Highcharts.chart(`chart-team-contacts-and-outcomes`, {
        chart: {
            type: 'area'
        },
        title: commonContactToChat.title,
        subtitle: "",
        xAxis: commonContactToChat.xAxis,
        yAxis: commonContactToChat.yAxis,
        plotOptions: {
            area: {
                fillOpacity: 0.5,
                dataLabels: {
                    enabled: true,
                }
            }
        },
        series: commonContactToChat.series
    });

    //
    const commonLeadsToCart = {!! json_encode($commonLeadsToCart) !!};
    Highcharts.chart(`chart-team-leads`, {
        chart: {
            type: 'area'
        },
        title: commonLeadsToCart.title,
        subtitle: "",
        xAxis: commonLeadsToCart.xAxis,
        yAxis: commonLeadsToCart.yAxis,
        plotOptions: {
            area: {
                fillOpacity: 0.5,
                dataLabels: {
                    enabled: true,
                }
            }
        },
        series: commonLeadsToCart.series
    });
</script>
@stop
