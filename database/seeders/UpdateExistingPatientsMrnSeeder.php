<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateExistingPatientsMrnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Updating existing patients without MRN...');
        
        // Cari pasien yang belum punya MRN atau MRN kosong
        $patientsWithoutMrn = Patient::where(function ($query) {
            $query->whereNull('medical_record_number')
                  ->orWhere('medical_record_number', '')
                  ->orWhere('medical_record_number', 'LIKE', 'RM001%'); // Update format lama
        })->get();
        
        $this->command->info("Found {$patientsWithoutMrn->count()} patients without proper MRN");
        
        foreach ($patientsWithoutMrn as $patient) {
            $oldMrn = $patient->medical_record_number;
            
            // Generate MRN baru
            $newMrn = Patient::generateMedicalRecordNumber();
            
            // Pastikan unique
            while (!Patient::isMrnUnique($newMrn, $patient->id)) {
                $newMrn = Patient::generateMedicalRecordNumber();
            }
            
            $patient->update(['medical_record_number' => $newMrn]);
            
            $this->command->line("Updated: {$patient->name} | {$oldMrn} → {$newMrn}");
        }
        
        $this->command->info('✅ All patients now have proper MRN format');
        
        // Tampilkan statistik
        $totalPatients = Patient::count();
        $uniqueMrns = Patient::distinct('medical_record_number')->count();
        
        $this->command->table(
            ['Metric', 'Value'],
            [
                ['Total Patients', $totalPatients],
                ['Unique MRNs', $uniqueMrns],
                ['Status', $totalPatients === $uniqueMrns ? '✅ All Unique' : '❌ Duplicates Found'],
            ]
        );
    }
}