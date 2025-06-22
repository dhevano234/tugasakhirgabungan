<?php
namespace App\Filament\Dokter\Resources\MedicalRecordResource\Pages;

use App\Filament\Dokter\Resources\MedicalRecordResource;
use App\Models\Patient;
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
        
        return $data;
    }

    // Mount function untuk handle parameter dari queue
    public function mount(): void
    {
        parent::mount();
        
        // Check untuk parameter dari queue
        $patientId = request()->get('patient_id');
        $queueNumber = request()->get('queue_number');
        $serviceName = request()->get('service');
        
        if ($patientId) {
            $patient = Patient::find($patientId);
            
            if ($patient) {
                // Auto-populate patient field
                $this->form->fill([
                    'patient_id' => $patientId,
                ]);
                
                // Show notification dengan info pasien
                Notification::make()
                    ->title('Pasien Dari Antrian')
                    ->body("Auto-selected: {$patient->medical_record_number} - {$patient->name}" . 
                           ($queueNumber ? " (Antrian: {$queueNumber})" : ""))
                    ->success()
                    ->duration(5000)
                    ->send();
                    
                // Update page title jika ada queue number
                if ($queueNumber) {
                    static::$title = "Rekam Medis - Antrian {$queueNumber}";
                }
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