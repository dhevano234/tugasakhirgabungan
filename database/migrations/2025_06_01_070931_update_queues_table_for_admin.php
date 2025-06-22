<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            // Pastikan kolom patient_id ada (mungkin sudah ada)
            if (!Schema::hasColumn('queues', 'patient_id')) {
                $table->foreignId('patient_id')->nullable()->after('service_id')->constrained()->cascadeOnDelete();
            }
            
            // Pastikan kolom status ada dengan nilai default
            if (!Schema::hasColumn('queues', 'status')) {
                $table->string('status')->default('waiting')->after('number');
            }
            
            // Pastikan kolom timestamp ada
            if (!Schema::hasColumn('queues', 'called_at')) {
                $table->timestamp('called_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('queues', 'served_at')) {
                $table->timestamp('served_at')->nullable()->after('called_at');
            }
            if (!Schema::hasColumn('queues', 'finished_at')) {
                $table->timestamp('finished_at')->nullable()->after('served_at');
            }
            if (!Schema::hasColumn('queues', 'canceled_at')) {
                $table->timestamp('canceled_at')->nullable()->after('finished_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->dropColumn(['patient_id', 'status', 'called_at', 'served_at', 'finished_at', 'canceled_at']);
        });
    }
};