<?php
// File: app/Filament/Resources/DoctorScheduleResource/Pages/CreateDoctorSchedule.php

namespace App\Filament\Resources\DoctorScheduleResource\Pages;

use App\Filament\Resources\DoctorScheduleResource;
use App\Models\DoctorSchedule;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateDoctorSchedule extends CreateRecord
{
    protected static string $resource = DoctorScheduleResource::class;

    protected static ?string $title = 'Tambah Jadwal Dokter';

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Simpan Jadwal'),
            $this->getCancelFormAction(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Extract days from the form data
        $days = $data['days'] ?? [];
        unset($data['days']); // Remove from data array

        $created = 0;
        $skipped = 0;
        $doctorName = $data['doctor_name'];

        foreach ($days as $day) {
            // Check if schedule already exists for this doctor and day
            $exists = DoctorSchedule::where('doctor_name', $doctorName)
                ->where('service_id', $data['service_id'])
                ->where('day_of_week', $day)
                ->exists();

            if (!$exists) {
                // Create new schedule for this day
                $scheduleData = $data;
                $scheduleData['day_of_week'] = $day;
                
                DoctorSchedule::create($scheduleData);
                $created++;
            } else {
                $skipped++;
            }
        }

        // Show appropriate notification
        if ($created > 0) {
            $message = "Berhasil membuat {$created} jadwal untuk {$doctorName}";
            if ($skipped > 0) {
                $message .= " ({$skipped} jadwal dilewati karena sudah ada)";
            }
            
            Notification::make()
                ->title('Jadwal Berhasil Dibuat')
                ->body($message)
                ->success()
                ->duration(5000)
                ->send();
        } else {
            Notification::make()
                ->title('Tidak Ada Jadwal Baru')
                ->body('Semua jadwal untuk dokter ini sudah ada')
                ->warning()
                ->send();
        }

        // Return the first created record or create a dummy one for the redirect
        // Since we're creating multiple records, we need to return something
        return DoctorSchedule::where('doctor_name', $doctorName)
            ->where('service_id', $data['service_id'])
            ->first() ?? new DoctorSchedule();
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        // Disable default notification since we're using custom ones
        return null;
    }
}