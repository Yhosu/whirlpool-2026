<?php

namespace App\Livewire\Pages\Auth;

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin-clean')]
class Login extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        session()->flash('status', 'Sesión iniciada correctamente!');

        $this->redirect('/admin/node-list/participant-coupon');
    }

    public function render()
    {
        // \Log::info('render Login');
        return view('livewire.pages.auth.login');
    }
}
