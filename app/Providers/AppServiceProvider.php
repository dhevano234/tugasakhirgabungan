<?php
// File: app/Providers/AppServiceProvider.php
// Load audio system yang sesuai untuk setiap panel

namespace App\Providers;

use App\Services\QueueService;
use App\Services\ThermalPrinterService;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ThermalPrinterService::class, function ($app) {
            return new ThermalPrinterService();
        });

        $this->app->singleton(QueueService::class, function ($app) {
            return new QueueService();
        });
    }

    public function boot(): void
    {
        // LOAD AUDIO SYSTEM SESUAI PANEL
        
        // Untuk Panel Admin: Load queue-audio.js (global system)
        // Untuk Panel Dokter: Audio system ada di header.blade.php (embedded)
        // Untuk Kiosk/Public: Load thermal-printer.js
        
        FilamentAsset::register([
            Js::make('thermal-printer', asset('js/thermal-printer.js')),
            Js::make('queue-audio', asset('js/queue-audio.js')), // Hanya untuk admin panel
        ]);
        
        // Catatan: Panel dokter akan menggunakan sistem audio embedded di header.blade.php
        // sehingga tidak perlu load queue-audio.js global
    }   
}