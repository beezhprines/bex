@extends('adminlte::page')

@section('content_header')
<x-week-header header="Контакты"></x-week-header>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <strong>Контакты на текущую неделю</strong>
            </div>
            <div class="card-body p-0">
                <form id="contacts-form" action="{{ route('contacts.saveMany') }}" method="post">
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
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="position-fixed" style="min-width:300px">
            <div class="card">
                <div class="card-body">
                    <button id="contacts-form-btn" class="btn btn-block btn-warning">{{ __('common.save') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section("js")
<script>
    (function($) {
        $("#contacts-form-btn").on("click", function() {
            $("#contacts-form").submit();
        });
    })(jQuery);
</script>
@stop
