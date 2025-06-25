<?php
// File: app/Models/Queue.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Queue extends Model
{
    protected $fillable = [
        'counter_id',
        'service_id',
        'user_id', // GANTI dari patient_id ke user_id
        'number',
        'status',
        'called_at',
        'served_at',
        'canceled_at',
        'finished_at',
    ];

    protected $casts = [
        'called_at' => 'datetime',
        'served_at' => 'datetime', 
        'canceled_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    // Relationship yang sudah ada
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function counter(): BelongsTo
    {
        return $this->belongsTo(Counter::class);
    }

    // Relationship ke user (bukan patient)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship ke medical record
    public function medicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class);
    }

    // Helper methods untuk status
    public function canEdit(): bool
    {
        return in_array($this->status, ['waiting']);
    }

    public function canCancel(): bool
    {
        return in_array($this->status, ['waiting']);
    }

    public function canPrint(): bool
    {
        return in_array($this->status, ['waiting', 'serving', 'finished']);
    }

    // Accessor untuk format tanggal
    public function getFormattedTanggalAttribute(): string
    {
        return $this->created_at->format('d F Y');
    }

    // Accessor untuk status badge
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'waiting' => 'warning',
            'serving' => 'info',
            'finished' => 'success',
            'canceled' => 'danger',
            default => 'secondary'
        };
    }
}