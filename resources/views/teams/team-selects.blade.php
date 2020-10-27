<div class="form-group">
    <label>
        Команда:
    </label>
    <select class="form-control selectpicker linkable" data-dropdown-align-right="true" data-live-search="true" data-size="10" title="Выберите команду">
        @forelse($teams as $team)
        <option data-link="{{ route($route, ['team' => $team->id]) }}" value="{{ $team->id }}" @if ( $team->id == $current->id) selected @endif>
            {{ $team->title}}
        </option>
        @empty
        <option value="">
            @lang('common.no-data')
        </option>
        @endforelse
    </select>
</div>
