<?php

namespace App\Livewire\Pages\Admin;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Component;

class ExamplePage extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {

        $this->form->fill($this->data);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('title')
                ->label('Título')
                ->required(),
            MarkdownEditor::make('content')
                ->label('Contenido'),
            Toggle::make('is_published')
                ->label('Publicado')
                ->onColor('success'),
        ];
    }

    public function create(): void
    {
        $data = $this->form->getState();

        Notification::make()
            ->title('Datos guardados con éxito')
            ->success()
            ->send();

        $this->form->fill();
    }

    public function render()
    {
        return view('livewire.pages.admin.example-page')
            ->layout('layouts.admin');
    }
}
