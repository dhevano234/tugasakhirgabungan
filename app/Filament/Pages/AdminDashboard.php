<?php
// File: app/Filament/Pages/AdminDashboard.php
// Simple Dashboard Component untuk Admin Panel

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AdminDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static string $view = 'filament.pages.admin-dashboard';
    
    protected static ?string $title = 'Dashboard';
    
    protected static ?string $navigationLabel = 'Dashboard';

    // Method untuk handle audio testing (development only)
    public function testAudio()
    {
        if (!app()->environment('local')) {
            return;
        }

        $this->dispatch('queue-called', 'Test audio dari dashboard admin panel');
    }
}