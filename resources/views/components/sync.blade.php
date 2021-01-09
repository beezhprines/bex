@if (week()->start() >= week()->beforeWeeks(2, week()->monday(isodate())) )
<a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('loader').style.display = 'block'; document.getElementById('sync-form').submit();">
    Пересчитать неделю
</a>
<form id="sync-form" action="{{ route('managers.sync') }}" method="POST">
    @csrf
    <input type="hidden" name="day" value="{{ request()->query('day') }}">
</form>
@else
<a class="dropdown-item" href="#" onclick="event.preventDefault();">
    Пересчет запрещен
</a>
@endif
