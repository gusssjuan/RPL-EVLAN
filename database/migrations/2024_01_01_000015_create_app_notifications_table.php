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
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('event_id')->nullable()->constrained('events')->nullOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->enum('jenis', [
                'penugasan',
                'deadline',
                'status_tugas',
                'undangan',
                'pendaftaran',
                'verifikasi_vendor'
            ]);
            $table->string('pesan');
            $table->string('tautan')->nullable();
            $table->string('dedupe_key')->nullable()->unique();
            $table->timestamp('dibaca_at')->nullable();
            $table->timestamp('waktu_kirim')->useCurrent();
            $table->timestamps();

            $table->index(['user_id', 'dibaca_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
    }
};

