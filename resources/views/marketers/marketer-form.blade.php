<form action="{{ $route }}" method="post">
    @csrf
    @method($method)

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Имя</label>
                <input type="text" name="name" class="form-control form-control-sm" value="{{ $marketer->name ?? null }}" required>
            </div>
        </div>
        <div class="col-md-6">
            @include('users.user-form', ['user' => $marketer->user ?? null])</div>
    </div>

    <div class="form-group text-right">
        <button type="submit" class="btn btn-warning btn-sm">Сохранить</button>
    </div>
</form>
