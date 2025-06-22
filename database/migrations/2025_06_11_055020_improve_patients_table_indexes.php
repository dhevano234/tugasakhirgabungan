<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Pastikan medical_record_number tidak null dan unik
            $table->string('medical_record_number', 20)->nullable(false)->change();
            
            // Tambah index untuk performa pencarian
            $table->index(['medical_record_number'], 'idx_patients_mrn');
            $table->index(['name'], 'idx_patients_name');
            $table->index(['phone'], 'idx_patients_phone');
            $table->index(['created_at'], 'idx_patients_created');
            
            // Tambah index composite untuk pencarian gabungan
            $table->index(['name', 'birth_date'], 'idx_patients_name_birth');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex('idx_patients_mrn');
            $table->dropIndex('idx_patients_name');
            $table->dropIndex('idx_patients_phone');
            $table->dropIndex('idx_patients_created');
            $table->dropIndex('idx_patients_name_birth');
            
            // Revert medical_record_number back to nullable
            $table->string('medical_record_number')->nullable()->change();
        });
    }
};