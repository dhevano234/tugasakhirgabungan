<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DokterSeeder extends Seeder
{
    public function run(): void
    {
    

        // Sample Patients
        Patient::create([
            'medical_record_number' => 'RM001',
            'name' => 'Ahmad Suryadi',
            'birth_date' => '1990-05-15',
            'gender' => 'male',
            'address' => 'Jl. Raya Banjaran No. 123, Bandung',
            'phone' => '081234567890',
            'blood_type' => 'O+',
        ]);

        Patient::create([
            'medical_record_number' => 'RM002',
            'name' => 'Siti Nurhaliza',
            'birth_date' => '1985-08-22',
            'gender' => 'female',
            'address' => 'Jl. Sudirman No. 456, Bandung',
            'phone' => '081234567892',
            'blood_type' => 'A+',
            'allergies' => 'Alergi seafood',
        ]);
    }
}