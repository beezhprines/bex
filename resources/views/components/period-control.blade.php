<form action="{{ $route }}" class="form-inline mb-2" method="GET">
    <label class="m-2">Период</label>
    <label class="sr-only" for="startDate">Начало</label>
    <div class="input-group date m-2">
        <div class="input-group-prepend">
            <div class="input-group-text">
                <i class="fa fa-calendar"></i>
            </div>
        </div>
        <input type="text" class="form-control date-input" id="startDate" name="startDate" placeholder="Начало" value="{{ request()->query('startDate') }}">
    </div>
    -
    <label class="sr-only" for="endDate">Окончание</label>
    <div class="input-group date m-2">
        <div class="input-group-prepend">
            <div class="input-group-text">
                <i class="fa fa-calendar"></i>
            </div>
        </div>
        <input type="text" class="form-control date-input" id="endDate" name="endDate" placeholder="Окончание" value="{{ request()->query('endDate') }}">
    </div>
    <div class="m-2">
        <button type="submit" class="btn btn-default btn-sm">Обновить</button>
    </div>
</form>
