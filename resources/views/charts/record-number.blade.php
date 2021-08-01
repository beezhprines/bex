@extends('adminlte::page')

@section('content_header')
<x-week-header header="Количество записей"></x-week-header>
@stop

@section('content')
<x-period-control :route="route('charts.record-number.post')" button="Скачать" method="POST"></x-period-control>
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
</script>
@stop
