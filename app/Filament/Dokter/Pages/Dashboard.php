<?php
namespace App\Filament\Dokter\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    protected static string $view = 'filament.dokter.pages.dashboard';

    public function getViewData(): array
    {
        return [
            'user' => Auth::user(),
        ];
    }
}