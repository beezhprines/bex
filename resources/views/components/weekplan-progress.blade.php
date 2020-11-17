<div class="progress-container position-relative">
    @if (!$hideMilestones)
    <div class="milestones">
        @foreach($milestones as $milestone)
        @php
        $milestonePosition = $limit != 0 ? round($milestone['profit'] / $limit * 100) : 0;
        @endphp
        <span class="milestone badge text-left {{ $profit >= $milestone['profit'] ? 'badge-success' : 'badge-warning milestone-next' }}" style="left: calc({{ $milestonePosition }}% - 19px)">
            <span class="value">{{ price($milestone['profit']) }}</span>
            @if (!empty($milestone['bonus']))
            <br> {{ price($milestone['bonus']) }}
            @endif
        </span>
        @endforeach
    </div>
    @endif
    <div class="progress bg-warning">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success pr-1 pt-1" role="progressbar" style="width:{{$progress}}%">
            <span class="profit">{{ price($profit) }}</span>
        </div>
    </div>
</div>
