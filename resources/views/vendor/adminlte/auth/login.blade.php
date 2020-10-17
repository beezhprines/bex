@extends('adminlte::master')

@section('body')
<div class="login-page">
    <div class="login-box">
        <div class="card">
            <div class="card-body login-card-body">
                @if (session('status-registered'))
                <div class="alert alert-success">
                    {{ session('status-registered') }}
                </div>
                @endif

                <p class="login-box-msg">
                    <strong>Добро пожаловать</strong>
                </p>

                <form action="{{ route('login') }}" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="account" name="account" class="form-control @error('account') is-invalid @enderror" placeholder="Логин" autofocus autocomplete="account" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>

                        @error('account')
                        <span class="error invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Пароль" name="password" required autocomplete="current-password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>

                        @error('password')
                        <span class="error invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="row mb-3">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember">
                                    Запомнить
                                </label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Войти</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
                <hr>
                <div class="row">
                    <div class="col text-center">
                        Забыли пароль? Свяжитесь с вашим оператором
                    </div>
                    {{--
                    <div class="col">
                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">Забыли пароль?</a>
                    @endif
                </div>
                <div class="col text-right">
                    <a href="{{ route('register') }}" class="text-center">Регистрация</a>
                </div>
                --}}
            </div>
        </div>
    </div>
</div>
<div class="text-center">
    <small class="text-muted"><i class="fa fa-code-branch"></i> {{version()}}</small>
</div>
@stop
