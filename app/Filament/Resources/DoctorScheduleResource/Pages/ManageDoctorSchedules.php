<?php
// File: app/Filament/Resources/DoctorScheduleResource/Pages/ManageDoctorSchedules.php

namespace App\Filament\Resources\DoctorScheduleResource\Pages;

use App\Filament\Resources\DoctorScheduleResource;
use App\Models\DoctorSchedule;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class ManageDoctorSchedules extends ManageRecords
{
    protected static string $resource = DoctorScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Jadwal Dokter')
                ->icon('heroicon-o-plus')
                ->url(static::getResource()::getUrl('create')),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            // Bisa ditambahkan widget statistik nanti
        ];
    }
}