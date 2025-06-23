<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Queue extends Model
{
    protected $fillable = [
        'counter_id',
        'service_id',
        'user_id',        // Menggunakan user_id langsung
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

    // Relationship langsung ke User (bukan Patient)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if queue can be edited
     */
    public function canEdit(): bool
    {
        return in_array($this->status, ['waiting']) && 
               $this->created_at->isToday();
    }

    /**
     * Check if queue can be cancelled
     */
    public function canCancel(): bool
    {
        return in_array($this->status, ['waiting']);
    }

    /**
     * Check if queue can be printed
     */
    public function canPrint(): bool
    {
        return true; // Bisa print kapan saja
    }

    /**
     * Get formatted date
     */
    public function getFormattedTanggalAttribute(): string
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    /**
     * Get status badge color
     */
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