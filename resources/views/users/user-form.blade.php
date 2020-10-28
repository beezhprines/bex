<div class="form-group">
    <label>Логин</label>
    <input type="text" name="user[account]" class="form-control form-control-sm" value="{{ $user->account ?? null }}" required>
</div>
<div class="form-group">
    <label>Пароль</label>
    <input type="text" name="user[open_password]" class="form-control form-control-sm" value="{{ $user->open_password ?? null }}" disabled>
</div>
<div class="form-group">
    <label>Новый пароль</label>
    <input type="text" name="user[password]" class="form-control form-control-sm" value="">
</div>
<div class="form-group">
    <label>Email</label>
    <input type="email" name="user[email]" class="form-control form-control-sm" value="{{ $user->email ?? null }}">
</div>
<div class="form-group">
    <label>Телефон</label>
    <input type="text" name="user[phone]" class="form-control form-control-sm" value="{{ $user->phone ?? null }}">
</div>
