<?php
// File: app/Filament/Dokter/Resources/MedicalRecordResource/Pages/CreateMedicalRecord.php

namespace App\Filament\Dokter\Resources\MedicalRecordResource\Pages;

use App\Filament\Dokter\Resources\MedicalRecordResource;
use App\Models\User;
use App\Models\Queue;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CreateMedicalRecord extends CreateRecord
{
    protected static string $resource = MedicalRecordResource::class;

    protected static ?string $title = 'Buat Rekam Medis';

    // Override method untuk menghilangkan tombol "Create & Create Another"
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-set doctor_id dengan Auth facade yang sudah di-import
        $data['doctor_id'] = Auth::id();
        
        // ✅ VALIDASI: Pastikan user_id adalah role 'user'
        if (isset($data['user_id'])) {
            $user = User::find($data['user_id']);
            if (!$user || $user->role !== 'user') {
                throw new \Exception('Hanya pasien dengan role user yang dapat dibuatkan rekam medis.');
            }
        }
        
        return $data;
    }

    // Mount function untuk handle parameter dari queue
    public function mount(): void
    {
        parent::mount();
        
        // Check untuk parameter dari queue - UBAH dari patient_id ke user_id
        $userId = request()->get('user_id');
        $queueNumber = request()->get('queue_number');
        $serviceName = request()->get('service');
        
        if ($userId) {
            $user = User::find($userId);
            
            // ✅ VALIDASI: Pastikan user ada dan role-nya 'user'
            if ($user && $user->role === 'user') {
                // Auto-populate user field
                $this->form->fill([
                    'user_id' => $userId,
                ]);
                
                // Show notification dengan info user
                Notification::make()
                    ->title('Pasien Dari Antrian')
                    ->body("Auto-selected: {$user->name} - {$user->email}" . 
                           ($queueNumber ? " (Antrian: {$queueNumber})" : ""))
                    ->success()
                    ->duration(5000)
                    ->send();
                    
                // Update page title jika ada queue number
                if ($queueNumber) {
                    static::$title = "Rekam Medis - Antrian {$queueNumber}";
                }
            } elseif ($user && $user->role !== 'user') {
                // ✅ WARNING: Jika user bukan role 'user'
                Notification::make()
                    ->title('Peringatan')
                    ->body("User {$user->name} bukan pasien (role: {$user->role}). Silakan pilih pasien yang valid.")
                    ->warning()
                    ->duration(8000)
                    ->send();
            } else {
                // ✅ ERROR: Jika user tidak ditemukan
                Notification::make()
                    ->title('Error')
                    ->body("User dengan ID {$userId} tidak ditemukan.")
                    ->danger()
                    ->duration(5000)
                    ->send();
            }
        }
    }
    
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Rekam medis berhasil dibuat';
    }
    
    // Auto-finish queue setelah rekam medis dibuat
    protected function afterCreate(): void
    {
        // Optional: Auto-finish queue jika ada parameter queue
        $queueNumber = request()->get('queue_number');
        
        if ($queueNumber) {
            // Find and finish the queue
            $queue = Queue::where('number', $queueNumber)
                ->whereDate('created_at', today())
                ->first();
                
            if ($queue && in_array($queue->status, ['waiting', 'serving'])) {
                $queue->update([
                    'status' => 'finished',
                    'finished_at' => now(),
                ]);
                
                Notification::make()
                    ->title('Antrian Selesai')
                    ->body("Antrian {$queueNumber} otomatis ditandai selesai")
                    ->success()
                    ->send();
            }
        }
    }
}