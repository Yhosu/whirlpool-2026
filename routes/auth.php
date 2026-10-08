<?php

use App\Livewire\Pages\Auth\Login;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Route::get('auth/register', 'pages.auth.register')
    //     ->name('register');

    Route::get('auth/login', Login::class)->name('auth.login');
});
