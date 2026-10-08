<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ManageConsul extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';

    protected static ?string $navigationGroup = 'CONSUL';

    protected static ?string $title = 'Gestión General Consul';

    protected static string $view = 'filament.pages.manage-consul';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
