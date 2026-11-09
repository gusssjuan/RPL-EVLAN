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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leader_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('nama_event');
            $table->text('tujuan');
            $table->date('tanggal');
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->string('lokasi');
            $table->string('target_peserta');
            $table->unsignedInteger('jumlah_peserta');
            $table->enum('status_event', ['perencanaan', 'selesai'])->default('perencanaan');
            $table->text('evaluasi')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->string('penyelenggara')->nullable();
            $table->boolean('is_publik')->default(false);
            $table->boolean('pendaftaran_dibuka')->default(false);
            $table->date('batas_pendaftaran')->nullable();
            $table->unsignedInteger('kuota')->nullable();
            $table->timestamps();

            $table->index('leader_id');
            $table->index('category_id');
            $table->index('tanggal');
            $table->index(['is_publik', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

