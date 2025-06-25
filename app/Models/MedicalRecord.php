<?php
// File: app/Models/MedicalRecord.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    // ✅ TAMBAH treatment_plan dan kolom lain yang ada di database
    protected $fillable = [
        'user_id',              
        'doctor_id', 
        'chief_complaint',      // Required
        'vital_signs',          // Optional
        'diagnosis',            // Required
        'prescription',         // Optional
        'additional_notes',     // Optional
        
        // ✅ TAMBAH kolom yang ada di database (agar tidak error saat insert)
        'treatment_plan',       // Optional (kolom lama yang tidak dipakai form tapi ada di DB)
        'history_of_present_illness', // Optional (kolom lama)
        'physical_examination', // Optional (kolom lama)
        'follow_up_date',       // Optional (kolom lama)
        'queue_id',             // Optional (kolom lama)
    ];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    // ✅ RELATIONSHIP: User (pastikan hanya role 'user')
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ✅ RELATIONSHIP: User yang HANYA role 'user' 
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->where('role', 'user');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function queue(): BelongsTo
    {
        return $this->belongsTo(Queue::class);
    }

    // ✅ SCOPE: Hanya rekam medis dari user dengan role 'user'
    public function scopeForPatients($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('role', 'user');
        });
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

    // Accessor untuk mendapatkan nama lengkap pasien dengan email
    public function getUserFullNameAttribute(): string
    {
        return $this->user ? 
            "{$this->user->name} - {$this->user->email}" : 
            'Unknown User';
    }

    // ✅ CHECK: Apakah user adalah pasien (role 'user')
    public function isPatientValid(): bool
    {
        return $this->user && $this->user->role === 'user';
    }

    // Accessor untuk format tanggal pemeriksaan
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('d F Y, H:i');
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

    // ✅ BOOT: Validasi otomatis saat create/update
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($medicalRecord) {
            // Validasi user_id harus role 'user'
            if ($medicalRecord->user_id) {
                $user = User::find($medicalRecord->user_id);
                if (!$user || $user->role !== 'user') {
                }
            }
        });

        static::updating(function ($medicalRecord) {
            // Validasi user_id harus role 'user' saat update
            if ($medicalRecord->isDirty('user_id') && $medicalRecord->user_id) {
                $user = User::find($medicalRecord->user_id);
                if (!$user || $user->role !== 'user') {
                }
            }
        });
    }
}