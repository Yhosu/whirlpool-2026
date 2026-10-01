@section('head')
    <link rel="stylesheet" href="{{ asset('assets/templates/admin/resources/css/others/auth/authentication.css') }}">
    <link rel="icon" href="{{ asset('assets/img/favicon.ico') }}" type="image/x-icon">
@endsection

@section('title', 'Login')

<div class="auth-wrapper auth-basic px-2">
    <div class="auth-inner my-2">
        <div class="card mb-0">
            <div class="card-body">
                <a href="{{ url('/') }}" class="brand-logo">
                    <img class="login-logo" src="{{ asset('assets/img/logotipo.png') }}" alt="{{ config('title') }}">
                </a>

                <h4 class="card-title mb-1">Bienvenido!</h4>
                {{-- <p class="card-text mb-2">Iniciar sesión</p> --}}

                @if ($errors->has('login_error'))
                    <x-cy-alert type="error">
                        {{ $errors->first('login_error') }}
                    </x-cy-alert>
                @endif

                <form class="auth-login-form mt-2" wire:submit="login" method="POST">
                    <div class="mb-1">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" wire:model="form.email" name="email" placeholder="usuario@ejemplo.com" aria-describedby="email" tabindex="1" autofocus required />
                    </div>

                    <div class="mb-1">
                        <div class="d-flex justify-content-between">
                            <label class="form-label" for="password">Contraseña</label>
                        </div>
                        <div class="input-group input-group-merge form-password-toggle">
                            <input type="password" class="form-control form-control-merge" id="password" wire:model="form.password" name="password" tabindex="2" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" required />
                            <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                        </div>
                    </div>
                    <div class="mb-1">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember-me" wire:model="form.remember" name="remember" tabindex="3" />
                            <label class="form-check-label" for="remember-me">Recordarme</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" tabindex="4">Iniciar sesión</button>
                </form>
            </div>
        </div>
    </div>
</div>
