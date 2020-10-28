@extends('adminlte::page')

@section('content_header')
<h4>
    Бонусы
</h4>
@stop

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <div class="card-title">
                    Бонусы менеджера
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('configurations.update', ['configuration' => $bexManagerProfit->id]) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>{{ $bexManagerProfit->title }}</label>
                        <input type="text" name="value" class="form-control form-control-sm" value="{{ $bexManagerProfit->value * 100 }}" required>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-warning btn-sm">Сохранить</button>
                    </div>
                </form>


                <form action="{{ route('configurations.update', ['configuration' => $bexManagerMilestones->id]) }}" method="post">
                    @csrf
                    @method('PUT')
                    <label>{{ $bexManagerMilestones->title }}</label>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <th>
                                    Сумма
                                </th>
                                <th>
                                    Вознаграждение
                                </th>
                                <th class="text-center">
                                    <i class="fa fa-trash"></i>
                                </th>
                            </thead>
                            <tbody>
                                @foreach(json_decode($bexManagerMilestones->value, true) as $key => $milestone)
                                <tr>
                                    <td>
                                        <input type="text" name="value[{{$key}}][profit]" class="form-control form-control-sm" value="{{ $milestone['profit'] }}" required>
                                    </td>
                                    <td>
                                        <input type="text" name="value[{{$key}}][bonus]" class="form-control form-control-sm" value="{{ $milestone['bonus'] }}" required>
                                    </td>
                                    <td class="align-middle text-center" title="Удалить">
                                        <i class="fa fa-trash text-danger delete-milestone"></i>
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td colspan="3" class="text-center">
                                        <a href="#" class="add-milestone">
                                            Добавить
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-warning btn-sm">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <div class="card-title">
                    Бонусы оператора
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('configurations.update', ['configuration' => $bexOperatorProfit->id]) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>{{ $bexOperatorProfit->title }}</label>
                        <input type="text" name="value" class="form-control form-control-sm" value="{{ $bexOperatorProfit->value * $bexOperatorPoint->value}}" required>
                        <small class="form-text text-muted">{{ $bexOperatorPoint->title }} = {{ $bexOperatorPoint->value }} тенге</small>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-warning btn-sm">Сохранить</button>
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
        var lastIndex = parseInt("{{ $key }}");
        $(".delete-milestone").on("click", function(e) {
            e.preventDefault();
            $(this).closest("tr").remove();
        });
        $(".add-milestone").on("click", function(e) {
            e.preventDefault();
            lastIndex++;
            let tr = $(this).closest("tbody").find("tr").first().clone(true);
            tr.find('input').each(function(i, e) {
                $(e).val("");
            });
            tr.find("input").first().attr("name", "value[" + lastIndex + "][profit]");
            tr.find("input").last().attr("name", "value[" + lastIndex + "][bonus]");
            $(this).closest("tr").before(tr);
        });
    });
</script>
@stop
