<?php
// File: database/migrations/2025_06_25_make_treatment_plan_nullable.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            // Ubah kolom treatment_plan menjadi nullable
            if (Schema::hasColumn('medical_records', 'treatment_plan')) {
                $table->text('treatment_plan')->nullable()->change();
            }
            
            // Ubah kolom lain yang mungkin bermasalah juga menjadi nullable
            if (Schema::hasColumn('medical_records', 'history_of_present_illness')) {
                $table->text('history_of_present_illness')->nullable()->change();
            }
            
            if (Schema::hasColumn('medical_records', 'physical_examination')) {
                $table->text('physical_examination')->nullable()->change();
            }
            
            if (Schema::hasColumn('medical_records', 'follow_up_date')) {
                $table->date('follow_up_date')->nullable()->change();
            }
            
            // Pastikan kolom yang kita pakai juga nullable (kecuali yang required)
            if (Schema::hasColumn('medical_records', 'vital_signs')) {
                $table->text('vital_signs')->nullable()->change();
            }
            
            if (Schema::hasColumn('medical_records', 'prescription')) {
                $table->text('prescription')->nullable()->change();
            }
            
            if (Schema::hasColumn('medical_records', 'additional_notes')) {
                $table->text('additional_notes')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            // Kembalikan ke NOT NULL jika diperlukan
            if (Schema::hasColumn('medical_records', 'treatment_plan')) {
                $table->text('treatment_plan')->nullable(false)->change();
            }
            
            if (Schema::hasColumn('medical_records', 'history_of_present_illness')) {
                $table->text('history_of_present_illness')->nullable(false)->change();
            }
            
            if (Schema::hasColumn('medical_records', 'physical_examination')) {
                $table->text('physical_examination')->nullable(false)->change();
            }
        });
    }
};