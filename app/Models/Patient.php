<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Patient extends Model
{
    protected $fillable = [
        'medical_record_number',
        'name',
        'birth_date',
        'gender',
        'address',
        'phone',
        'emergency_contact',
        'blood_type',
        'allergies',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    // Auto-generate medical record number saat creating
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($patient) {
            if (empty($patient->medical_record_number)) {
                $patient->medical_record_number = self::generateMedicalRecordNumber();
            }
        });
    }

    /**
     * Generate unique medical record number
     * Format: RM-YYYYMMDD-XXXX
     * Contoh: RM-20250611-0001
     */
    public static function generateMedicalRecordNumber(): string
    {
        $today = Carbon::now()->format('Ymd');
        $prefix = "RM-{$today}-";
        
        // Cari nomor terakhir hari ini
        $lastRecord = self::where('medical_record_number', 'LIKE', $prefix . '%')
            ->orderBy('medical_record_number', 'desc')
            ->first();
        
        if ($lastRecord) {
            // Ambil 4 digit terakhir dan increment
            $lastNumber = (int) substr($lastRecord->medical_record_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            // Nomor pertama hari ini
            $newNumber = 1;
        }
        
        // Format dengan leading zeros (4 digit)
        $formattedNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        
        return $prefix . $formattedNumber;
    }

    /**
     * Generate alternative MRN format if needed
     * Format: RM-XXXXXX (6 digit sequential)
     */
    public static function generateSimpleMRN(): string
    {
        $prefix = "RM-";
        
        // Cari nomor terakhir
        $lastRecord = self::where('medical_record_number', 'LIKE', $prefix . '%')
            ->where('medical_record_number', 'REGEXP', '^RM-[0-9]{6}$')
            ->orderBy('medical_record_number', 'desc')
            ->first();
        
        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->medical_record_number, 3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class);
    }

    public function getAgeAttribute(): int
    {
        return $this->birth_date->diffInYears(now());
    }

    public function getGenderLabelAttribute(): string
    {
        return $this->gender === 'male' ? 'Laki-laki' : 'Perempuan';
    }

    /**
     * Scope untuk mencari pasien berdasarkan MRN atau nama
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('medical_record_number', 'LIKE', "%{$search}%")
              ->orWhere('name', 'LIKE', "%{$search}%")
              ->orWhere('phone', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Get formatted MRN for display
     */
    public function getFormattedMrnAttribute(): string
    {
        return $this->medical_record_number;
    }

    /**
     * Check if MRN is unique
     */
    public static function isMrnUnique(string $mrn, ?int $excludeId = null): bool
    {
        $query = self::where('medical_record_number', $mrn);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return !$query->exists();
    }
}