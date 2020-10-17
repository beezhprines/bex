@extends('adminlte::master')

@section('body')
<div class="login-page">
    <div class="login-box">
        <div class="card">
            <div class="card-body login-card-body">
                @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
                @endif

                <p class="login-box-msg">
                    Забыли пароль?
                </p>
                <p class="text-center">
                    Свяжитесь с вашим оператором
                </p>
                <p class="text-center">
                    <a href="{{ route('login') }}">
                        Войти
                    </a>
                </p>
                {{--
                <form action="{{ route('password.email') }}" method="post">
                @csrf
                <p>
                    Вам будет отправлена ссылка для восстановления пароля
                </p>
                <div class="input-group mb-3">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Введите Ваш email">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    @error('email')
                    <span class="error invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">
                            Запросить новый пароль
                        </button>
                    </div>
                    <!-- /.col -->
                </div>
                </form>
                --}}
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->
</div>
@stop
