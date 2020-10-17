@extends('adminlte::master')

@section('body')
<div class="register-page">
    <div class="register-box">
        <div class="card">
            <div class="card-body register-card-body">
                @if (session('register-error'))
                <div class="alert alert-danger">
                    {{ session('register-error') }}
                </div>
                @endif

                <p class="login-box-msg"> <strong>Регистрация</strong></p>
{{--
                <form action="{{ route('register') }}" method="post" id="register-form">
                    @csrf

                    <div class="input-group mb-3">
                        <input type="text" class="form-control @error('account') is-invalid @enderror" name="account" value="{{ old('account') }}" required autocomplete="account" placeholder="Логин" autofocus>
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
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email">
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
                    <div class="input-group mb-3">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Пароль">
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
                    <div class="input-group mb-3">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Повторите пароль">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="icheck-primary">
                            <input type="checkbox" class="@error('terms') is-invalid @enderror" id="agreeTerms" name="terms" value="true">
                            <label for="agreeTerms">
                                Я согласен(-на) с <a href="/docs/terms" target="_blank">правилами</a>
                            </label>

                            @error('terms')
                            <span class="error invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Зарегистрироваться</button>
                    </div>
                </form>
                --}}
                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-center">У меня уже есть аккаунт</a>
                </div>
            </div>
            <!-- /.form-box -->
        </div><!-- /.card -->
    </div>
    <!-- /.register-box -->
</div>
@stop

@section('js')
<script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_PUBLIC_KEY') }}"></script>
<script>
    $(document).ready(function() {
        $('#register-form').submit(function(event) {
            event.preventDefault();
            grecaptcha.ready(function() {
                grecaptcha.execute("{{ env('RECAPTCHA_PUBLIC_KEY') }}", {
                    action: 'register_user'
                }).then(function(token) {
                    $('#register-form').prepend('<input type="hidden" name="recaptcha_token" value="' + token + '">');
                    $('#register-form').unbind('submit').submit();
                });;
            });
        });
    });
</script>
@stop
