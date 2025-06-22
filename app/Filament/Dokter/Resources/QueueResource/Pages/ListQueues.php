<?php
namespace App\Filament\Dokter\Resources\QueueResource\Pages;

use App\Filament\Dokter\Resources\QueueResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQueues extends ListRecords
{
    protected static string $resource = QueueResource::class;
    protected static ?string $title = 'Kelola Antrian';

    protected function getHeaderActions(): array
    {
        return [
            // Hapus semua tombol audio - hanya tampilkan daftar antrian
        ];
    }
}