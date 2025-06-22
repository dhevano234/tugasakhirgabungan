<?php
// File: database/migrations/2025_06_21_create_doctor_schedules_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('doctor_name'); // Nama dokter
            $table->foreignId('service_id')->constrained()->cascadeOnDelete(); // Poli dari services
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']); // Hari
            $table->time('start_time'); // Jam mulai
            $table->time('end_time'); // Jam selesai
            $table->boolean('is_active')->default(true); // Status aktif
            $table->string('foto')->nullable(); // <--- tambahkan ini
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['doctor_name', 'day_of_week']);
            $table->index(['service_id', 'day_of_week']);
            $table->index(['is_active', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_schedules');
    }
};