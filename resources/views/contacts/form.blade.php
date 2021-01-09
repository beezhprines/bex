@extends('adminlte::page')

@section('content_header')
<x-week-header header="Контакты"></x-week-header>
@stop

@section('content')
<div class="card card-warning card-outline">
    <div class="card-header">
        <strong>Контакты на текущую неделю</strong>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('contacts.saveMany') }}" method="post">
            @csrf
            <input type="hidden" name="date" value="{{ week()->end() }}">
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <th>
                            Команда
                        </th>
                        @foreach($contactTypes as $contactType)
                        <th>
                            {{ $contactType->title }}
                        </th>
                        @endforeach
                    </thead>
                    <tbody>
                        @foreach($teams as $team)
                        <tr>
                            <td class="align-middle">
                                {{ $team->title }}
                            </td>
                            @php
                            $contacts = $team->contacts()->where("date", week()->end())->get();
                            $previousWeekContacts = $team->contacts()->where("date", week()->previous(week()->end()))->get();
                            @endphp
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
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="form-group px-4">
                <input type="submit" class="btn btn-sm btn-warning" value="{{ __('common.save') }}" />
            </div>
        </form>
    </div>
</div>
@stop
