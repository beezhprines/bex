<ul class="list-inline text-right mb-0">
    <li class="list-inline-item">
        <button data-startDate="{{ week()->previous(week()->start()) }}" data-endDate="{{ week()->previous(week()->end()) }}" class="btn btn-transparent week-control" type="button" title="Предыдущая неделя">
            <i class="fa fa-chevron-left"></i>
        </button>
    </li>
    <li class="list-inline-item">{{ $slot }}</li>
    @if (week()->end() < isodate())
    <li class="list-inline-item">
        <button data-startDate="{{ week()->next(week()->start()) }}" data-endDate="{{ week()->next(week()->end()) }}" class="btn btn-transparent week-control" type="button" title="Следующая неделя">
            <i class="fa fa-chevron-right"></i>
        </button>
    </li>
    @endif
</ul>
