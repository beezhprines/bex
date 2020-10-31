<div class="table-responsive">
    <table class="table table-sm table-striped">
        <thead>
            <th>
                Услуга
            </th>
            <th>
                Цена
            </th>
            <th>
                Комиссия
            </th>
            <th class="text-center">
                Конверсия
            </th>
            <th class="text-center">
                Длительность
            </th>
        </thead>
        <tbody>
            @foreach($services as $service)
            <tr>
                <td class="align-middle @if($service->conversion) text-bold @endif">
                    {{ $service->title }}
                </td>
                <td class="align-middle">
                    {{ price($service->price) }}
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="services[{{$service->id}}][comission]" value="{{ $service->comission }}" />
                </td>
                <td class="text-center align-middle">
                    <div class="custom-control custom-switch">
                        <input type="hidden" name="services[{{$service->id}}][conversion]" value="{{ optional($service)->conversion ?? 0 }}">
                        <input type="checkbox" id="conversion_{{$service->id}}" class="custom-control-input" {{ optional($service)->conversion ? 'checked' : '' }}>
                        <label class="custom-control-label" for="conversion_{{$service->id}}">
                        </label>
                    </div>
                </td>
                <td class="text-center align-middle">
                    {{ secondsToTime(intval($service->seance_length)) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
