@if (!empty($contactTypes))
<form action="{{ route('contacts.saveMany') }}" method="post">
    @csrf
    <input type="hidden" name="date" value="{{ week()->end() }}">
    <div class="table-responsive">
        <table class="table table-sm table-borderless">
            <thead>
                @foreach($contactTypes as $contactType)
                <th>
                    {{ $contactType->title }}
                </th>
                @endforeach
            </thead>
            <tbody>
                <tr>
                    @foreach($contacts as $key => $contact)
                    <td>
                        <input type="text" class="form-control form-control-sm" name="contacts[{{$contact->id}}][amount]" value="{{ $contact->amount }}" />
                        @php
                        $previousWeekAmount = $previousWeekContacts[$key]->amount ?? 0;
                        $increase = $contact->amount - $previousWeekAmount;
                        if ($contact->contactType->code == "instagram"){
                        $increase = $contact->amount;
                        }
                        @endphp
                        <small class="form-text text-muted">Прирост {{ $increase }}</small>
                    </td>
                    @endforeach
                </tr>
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
