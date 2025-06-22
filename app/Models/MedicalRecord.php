<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    // YANG DIUBAH - HANYA FIELD YANG DIPAKAI DI FORM
    protected $fillable = [
        'patient_id',
        'doctor_id', 
        'chief_complaint',           // Gejala/Keluhan Utama (Required)
        'vital_signs',               // Tanda Vital (Optional)
        'diagnosis',                 // Diagnosis (Required)
        'prescription',              // Resep Obat (Optional)
        'additional_notes',          // Catatan Tambahan (Optional)
        
    ];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function queue(): BelongsTo
    {
        return $this->belongsTo(Queue::class);
    }

    // Accessor untuk mendapatkan nama lengkap pasien dengan nomor RM
    public function getPatientFullNameAttribute(): string
    {
        return $this->patient ? 
            "{$this->patient->medical_record_number} - {$this->patient->name}" : 
            'Unknown Patient';
    }

    // Accessor untuk format tanggal pemeriksaan
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('d F Y, H:i');
    }

    // Scope untuk filter berdasarkan dokter
    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    // Scope untuk filter berdasarkan periode
    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Method untuk check apakah record memiliki resep
    public function hasPrescription(): bool
    {
        return !empty($this->prescription);
    }

    // Method untuk check apakah record memiliki catatan tambahan
    public function hasAdditionalNotes(): bool
    {
        return !empty($this->additional_notes);
    }

    // Method untuk mendapatkan summary singkat
    public function getSummary(): string
    {
        $summary = "Keluhan: " . substr($this->chief_complaint, 0, 50);
        if (strlen($this->chief_complaint) > 50) {
            $summary .= "...";
        }
        $summary .= " | Diagnosis: " . substr($this->diagnosis, 0, 30);
        if (strlen($this->diagnosis) > 30) {
            $summary .= "...";
        }
        return $summary;
    }
}