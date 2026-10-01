<?php

namespace App\Livewire\Components\Modals;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UserEdit extends Component
{
    public string $name = '';

    public string $last_name = '';

    public string $email = '';

    public string $username = '';

    public bool $active = true;

    public string $birthdate = '';

    public function mount(): void
    {
        $this->resetErrorBag(); // Resetea los errores
        $this->reset('name', 'last_name', 'email', 'username', 'active', 'birthdate');
        $this->resetFormFields();
    }

    public function resetFormFields(): void
    {
        $user = Auth::user();
        $this->name = $user->name ?? '';
        $this->last_name = $user->last_name ?? '';
        $this->email = $user->email ?? '';
        $this->username = $user->username ?? '';
        $this->active = boolval($user->active ?? true);
        $this->birthdate = $user->birthdate ?? '';
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        // $date = DateTime::createFromFormat('d-m-Y', $this->birthdate);
        // $this->birthdate = $date->format('Y-m-d'); // TODO: aún la fecha está mal, hay bugs cuando cambia y cuando por el formato

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'username' => ['required', 'string', 'max:255'],
            'active' => ['boolean'],
            'birthdate' => ['required', 'date'],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('account-updated', name: $user->name);
        $this->dispatch('profileUpdated');
    }

    public function getListeners(): array
    {
        return [
            'modalClosed' => 'mount',
        ];
    }

    public function render()
    {
        \Log::info('render components.modals.user-edit');

        return view('livewire.components.modals.user-edit');
    }
}
