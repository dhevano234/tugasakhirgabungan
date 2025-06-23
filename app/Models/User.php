<?php
// File: app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

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
     * Cek apakah data profil sudah lengkap untuk buat antrian
     */
    public function isProfileCompleteForQueue()
    {
        return !empty($this->phone) && 
               !empty($this->gender) && 
               !empty($this->birth_date) && 
               !empty($this->address);
    }

    /**
     * Get missing profile data untuk buat antrian
     */
    public function getMissingProfileData()
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
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is dokter
     */
    public function isDokter()
    {
        return $this->role === 'dokter';
    }

    /**
     * Check if user is pasien/user
     */
    public function isUser()
    {
        return $this->role === 'user';
    }

        public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'doctor_id');
    }
}