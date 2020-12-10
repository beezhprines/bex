@if (!empty($contactTypes))
<form action="{{ route('contacts.saveMany') }}" method="post">
    @csrf
    <div class="table-responsive">
        <table class="table table-sm table-borderless">
            <thead>
                <th></th>
                @foreach($contactTypes as $contactType)
                <th colspan="2">
                    {{ $contactType->title }}
                </th>
                @endforeach
            </thead>
            <tbody>
                @foreach($contacts as $date => $group)
                <tr>
                    <td class="align-middle">
                        {{ week()->weekTitles(date("D", strtotime($date))) }}
                    </td>
                    @foreach($group as $key => $contact)
                    <td>
                        <input type="text" class="form-control form-control-sm" name="contacts[{{ $contact['self']->id }}][team][{{ $contact['teamId'] }}][amount]" value="{{ $contact['amount'] }}" />
                    </td>
                    <td class="align-middle">
                        <span title="Прирост">
                            @if ($contact['amount'] > 0 && isset($last) && isset($last[$key]['amount']))
                                {{ $contact['amount'] - $last[$key]['amount'] }}
                            @else
                                0
                            @endif
                        </span>
                    </td>
                    @endforeach
                </tr>
                @php
                $last = $group;
                @endphp
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="form-group text-center">
        <input type="submit" class="btn btn-sm btn-warning" value="{{ __('common.save') }}" />
    </div>
</form>
@else
<div class="text-center p-2">
    @lang("common.no-data")
</div>
@endif
