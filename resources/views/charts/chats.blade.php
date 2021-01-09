@extends('adminlte::page')

@section('content_header')
<x-week-header header="Диаграммы чатов"></x-week-header>
@stop

@section('content')
<form action="{{ route('charts.chats') }}" class="form-inline" method="GET">
    <label class="m-2">Период</label>
    <label class="sr-only" for="startDate">Начало</label>
    <div class="input-group m-2">
        <div class="input-group-prepend">
            <div class="input-group-text">
                <i class="fa fa-calendar"></i>
            </div>
        </div>
        <input type="text" class="form-control" id="startDate" placeholder="Начало" value="{{ request()->query('startDate') }}">
    </div>
    <label class="sr-only" for="endDate">Окончание</label>
    <div class="input-group m-2">
        <div class="input-group-prepend">
            <div class="input-group-text">
                <i class="fa fa-calendar"></i>
            </div>
        </div>
        <input type="text" class="form-control" id="endDate" placeholder="Окончание" value="{{ request()->query('endDate') }}">
    </div>
    <div class="m-2">
        <button type="submit" class="btn btn-default btn-sm">Обновить</button>
    </div>
</form>

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
    const startDate = urlParams.get('startDate');
    const endDate = urlParams.get('endDate');

    chats.forEach(chat => {
        Highcharts.chart(`chart-team-${chat["id"]}`, {
            chart: {
                type: 'line'
            },
            title: {
                text: chat.data.title
            },
            subtitle: {
                text: `Период: с ${startDate} по ${endDate}`
            },
            xAxis: {
                categories: chat.data.x
            },
            yAxis: {
                title: {
                    text: 'Количество чатов'
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: false
                }
            },
            series: [{
                name: 'Сумма чатов',
                data: chat.data.y
            }]
        });
    });
</script>
@stop
