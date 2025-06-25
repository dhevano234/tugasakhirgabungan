<?php
// File: database/migrations/2025_06_25_rename_patient_id_to_user_id.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rename kolom di tabel medical_records
        if (Schema::hasColumn('medical_records', 'patient_id')) {
            Schema::table('medical_records', function (Blueprint $table) {
                // Drop foreign key constraint dulu
                $table->dropForeign(['patient_id']);
            });
            
            // Rename kolom
            Schema::table('medical_records', function (Blueprint $table) {
                $table->renameColumn('patient_id', 'user_id');
            });
            
            // Tambah foreign key constraint baru ke users table
            Schema::table('medical_records', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 2. Rename kolom di tabel queues (jika ada)
        if (Schema::hasColumn('queues', 'patient_id')) {
            Schema::table('queues', function (Blueprint $table) {
                // Drop foreign key constraint dulu
                $table->dropForeign(['patient_id']);
            });
            
            // Rename kolom
            Schema::table('queues', function (Blueprint $table) {
                $table->renameColumn('patient_id', 'user_id');
            });
            
            // Tambah foreign key constraint baru ke users table
            Schema::table('queues', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        // 1. Kembalikan kolom di tabel medical_records
        if (Schema::hasColumn('medical_records', 'user_id')) {
            Schema::table('medical_records', function (Blueprint $table) {
                // Drop foreign key constraint dulu
                $table->dropForeign(['user_id']);
            });
            
            // Rename kolom kembali
            Schema::table('medical_records', function (Blueprint $table) {
                $table->renameColumn('user_id', 'patient_id');
            });
            
            // Tambah foreign key constraint kembali ke patients table
            Schema::table('medical_records', function (Blueprint $table) {
                $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            });
        }

        // 2. Kembalikan kolom di tabel queues
        if (Schema::hasColumn('queues', 'user_id')) {
            Schema::table('queues', function (Blueprint $table) {
                // Drop foreign key constraint dulu
                $table->dropForeign(['user_id']);
            });
            
            // Rename kolom kembali
            Schema::table('queues', function (Blueprint $table) {
                $table->renameColumn('user_id', 'patient_id');
            });
            
            // Tambah foreign key constraint kembali ke patients table
            Schema::table('queues', function (Blueprint $table) {
                $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            });
        }
    }
};