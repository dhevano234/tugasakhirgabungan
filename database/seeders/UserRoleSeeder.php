<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Buat user admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@clinic.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Buat user dokter
        User::create([
            'name' => 'dr. Annisa Widiautami Mulyana',
            'email' => 'dokter1@clinic.com', 
            'password' => Hash::make('password'),
            'role' => 'dokter',
        ]);

         // Buat user dokter
        User::create([
            'name' => 'dr. Batari Nandini',
            'email' => 'dokter2@clinic.com', 
            'password' => Hash::make('password'),
            'role' => 'dokter',
        ]);
    }
}