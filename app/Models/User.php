<?php
// File: app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'gender',
        'birth_date',
        'address',
        'nomor_ktp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
        ];
    }

    /**
     * Boot method untuk set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Pastikan role default jika tidak diset
            if (empty($user->role)) {
                $user->role = 'user';
            }
        });
    }

    /**
     * Relationship ke Queue (antrian yang dibuat user)
     */
    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class);
    }

    /**
     * Relationship ke MedicalRecord (sebagai dokter)
     */
    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'doctor_id');
    }

    /**
     * Cek apakah data profil sudah lengkap untuk buat antrian
     */
    public function isProfileCompleteForQueue(): bool
    {
        return !empty($this->phone) && 
               !empty($this->gender) && 
               !empty($this->birth_date) && 
               !empty($this->address);
    }

    /**
     * Get missing profile data untuk buat antrian
     */
    public function getMissingProfileData(): array
    {
        $missing = [];
        
        if (empty($this->phone)) $missing[] = 'Nomor HP';
        if (empty($this->gender)) $missing[] = 'Jenis Kelamin';
        if (empty($this->birth_date)) $missing[] = 'Tanggal Lahir';
        if (empty($this->address)) $missing[] = 'Alamat';
        
        return $missing;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is dokter
     */
    public function isDokter(): bool
    {
        return $this->role === 'dokter';
    }

    /**
     * Check if user is pasien/user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Get user's age
     */
    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? $this->birth_date->diffInYears(now()) : null;
    }

    /**
     * Get formatted gender
     */
    public function getGenderLabelAttribute(): string
    {
        return match($this->gender) {
            'Laki-laki' => 'Laki-laki',
            'Perempuan' => 'Perempuan',
            'male' => 'Laki-laki',
            'female' => 'Perempuan',
            default => $this->gender ?? 'Tidak diketahui'
        };
    }
}