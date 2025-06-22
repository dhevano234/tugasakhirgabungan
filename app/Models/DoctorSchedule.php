<?php
// File: app/Models/DoctorSchedule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class DoctorSchedule extends Model
{
    protected $fillable = [
        'doctor_name',
        'service_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
        'foto'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship ke Service (Poli)
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Service::class);
    }

    /**
     * Relationship ke User (untuk nanti ketika terhubung)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get poli name from service
     */
    public function getPoliNameAttribute(): string
    {
        return $this->service ? $this->service->name : 'Unknown Service';
    }
    /**
     * Get formatted day name in Indonesian
     */
    public function getDayNameAttribute(): string
    {
        $days = [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu',
        ];

        return $days[$this->day_of_week] ?? $this->day_of_week;
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute(): string
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    /**
     * Get duration in hours
     */
    public function getDurationAttribute(): float
    {
        $start = Carbon::createFromFormat('H:i', $this->start_time->format('H:i'));
        $end = Carbon::createFromFormat('H:i', $this->end_time->format('H:i'));
        
        return $start->diffInHours($end, true);
    }

    /**
     * Check if schedule is active today
     */
    public function isActiveToday(): bool
    {
        $today = strtolower(now()->format('l')); // monday, tuesday, etc.
        return $this->is_active && $this->day_of_week === $today;
    }

    /**
     * Scope untuk jadwal aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk hari tertentu
     */
    public function scopeForDay($query, string $day)
    {
        return $query->where('day_of_week', strtolower($day));
    }

    /**
     * Scope untuk service tertentu
     */
    public function scopeForService($query, int $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    /**
     * Get all unique doctor names
     */
    public static function getUniqueDoctorNames(): array
    {
        return self::distinct('doctor_name')
            ->pluck('doctor_name')
            ->sort()
            ->values()
            ->toArray();
    }

    /**
     * Get schedule for specific doctor and day
     */
    public static function getScheduleForDoctorAndDay(string $doctorName, string $day): ?self
    {
        return self::where('doctor_name', $doctorName)
            ->where('day_of_week', strtolower($day))
            ->where('is_active', true)
            ->first();
    }
}