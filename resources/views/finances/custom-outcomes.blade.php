@extends('adminlte::page')

@section('content_header')
<h4>
    Расходы
</h4>
@stop

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <div class="card-title">
                    Месячные расходы на {{ viewdate($monthBudget->date)a }}
                </div>
            </div>
            <div class="card-body p-0">
                <form action="{{ route('finances.customOutcomes.update') }}" method="post">
                    @csrf
                    <input type="hidden" name="budget_id" value="{{$monthBudget->id}}">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <th>Название</th>
                                <th>Количество</th>
                                <th class="text-center"><i class="fa fa-trash"></i></th>
                            </thead>
                            <tbody>
                                @if (empty(json_decode($monthBudget->json, true)))
                                <tr data-index="0">
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="custom-outcomes[0][title]" value="" required />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="custom-outcomes[0][amount]" value="" required />
                                    </td>
                                    <td class="align-middle text-center" title="Удалить">
                                        <i class="fa fa-trash text-danger delete-outcome"></i>
                                    </td>
                                </tr>
                                @else
                                @foreach(json_decode($monthBudget->json, true) as $key => $outcome)
                                <tr data-index="{{$key + 1}}">
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="custom-outcomes[{{ $key + 1 }}][title]" value="{{ $outcome['title'] }}" required />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="custom-outcomes[{{ $key + 1 }}][amount]" value="{{ $outcome['amount'] }}" required />
                                    </td>
                                    <td class="align-middle text-center" title="Удалить">
                                        <i class="fa fa-trash text-danger delete-outcome"></i>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                                <tr>
                                    <td colspan="3" class="text-center">
                                        <a href="#" class="add-outcome">
                                            Добавить
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group text-right py-2 px-4">
                        <button type="submit" class="btn btn-sm btn-warning">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <div class="card-title">
                    Расходы текущую неделю
                </div>
            </div>
            <div class="card-body p-0">
                <form action="{{ route('finances.customOutcomes.update') }}" method="post">
                    @csrf
                    <input type="hidden" name="budget_id" value="{{$weekBudget->id}}">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <th>Название</th>
                                <th>Количество</th>
                                <th class="text-center"><i class="fa fa-trash"></i></th>
                            </thead>
                            <tbody>
                                @if (empty(json_decode($weekBudget->json, true)))
                                <tr data-index="0">
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="custom-outcomes[0][title]" value="" required />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="custom-outcomes[0][amount]" value="" required />
                                    </td>
                                    <td class="align-middle text-center" title="Удалить">
                                        <i class="fa fa-trash text-danger delete-outcome"></i>
                                    </td>
                                </tr>
                                @else
                                @foreach(json_decode($weekBudget->json, true) as $key => $outcome)
                                <tr data-index="{{$key + 1}}">
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="custom-outcomes[{{ $key + 1 }}][title]" value="{{ $outcome['title'] }}" required />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="custom-outcomes[{{ $key + 1 }}][amount]" value="{{ $outcome['amount'] }}" required />
                                    </td>
                                    <td class="align-middle text-center" title="Удалить">
                                        <i class="fa fa-trash text-danger delete-outcome"></i>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                                <tr>
                                    <td colspan="3" class="text-center">
                                        <a href="#" class="add-outcome">
                                            Добавить
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group text-right py-2 px-4">
                        <button type="submit" class="btn btn-sm btn-warning">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $(".delete-outcome").on("click", function(e) {
            e.preventDefault();
            $(this).closest("tr").remove();
        });
        $(".add-outcome").on("click", function(e) {
            e.preventDefault();
            let tr = $(this).closest("tbody").find("tr").first().clone(true);
            var lastIndex = $(this).closest("tbody").find("tr").last().prev().attr('data-index');
            lastIndex = parseInt(lastIndex) + 1;
            tr.attr('data-index', lastIndex);
            tr.find('input').each(function(i, e) {
                $(e).val("");
            });
            tr.find("input").first().attr("name", "custom-outcomes[" + lastIndex + "][title]");
            tr.find("input").last().attr("name", "custom-outcomes[" + lastIndex + "][amount]");
            $(this).closest("tr").before(tr);
        });
    });
</script>
@stop
