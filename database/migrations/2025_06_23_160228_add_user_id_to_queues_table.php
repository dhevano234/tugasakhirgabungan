// database/migrations/xxxx_add_user_id_to_queues_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            // Tambah kolom user_id
            $table->foreignId('user_id')->after('service_id')->constrained()->cascadeOnDelete();
            
            // Hapus patient_id jika ada
            if (Schema::hasColumn('queues', 'patient_id')) {
                $table->dropConstrainedForeignId('patient_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            
            // Restore patient_id jika perlu rollback
            $table->foreignId('patient_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }
};