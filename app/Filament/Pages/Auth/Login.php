<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    /**
     * Título principal que aparece arriba del formulario de login.
     * Puedes dejarlo en blanco ('') para no mostrar ningún título,
     * o cambiarlo por el texto que desees (ej: 'Iniciar sesión').
     */
    public function getHeading(): string | Htmlable
    {
        return '';
    }
}
