<?php
// File: database/migrations/2025_06_25_fix_queues_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            // Hapus kolom patient_id jika ada
            if (Schema::hasColumn('queues', 'patient_id')) {
                $table->dropConstrainedForeignId('patient_id');
            }
            
            // Tambahkan kolom user_id jika belum ada, atau ubah menjadi nullable
            if (!Schema::hasColumn('queues', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('service_id')->constrained()->cascadeOnDelete();
            } else {
                // Jika sudah ada, pastikan nullable
                $table->foreignId('user_id')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            // Kembalikan patient_id jika diperlukan
            if (!Schema::hasColumn('queues', 'patient_id')) {
                $table->foreignId('patient_id')->nullable()->after('service_id')->constrained()->cascadeOnDelete();
            }
            
            // Hapus user_id
            if (Schema::hasColumn('queues', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};