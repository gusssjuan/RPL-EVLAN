<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dedupe key perlu unik per pengguna, bukan global, agar reminder deadline
     * dapat dikirim ke penerima tugas dan Leader dengan kunci yang sama.
     */
    public function up(): void
    {
        Schema::table('app_notifications', function (Blueprint $table) {
            $table->dropUnique('app_notifications_dedupe_key_unique');
            $table->unique(['user_id', 'dedupe_key'], 'app_notifications_user_id_dedupe_key_unique');
        });
    }

    public function down(): void
    {
        Schema::table('app_notifications', function (Blueprint $table) {
            $table->dropUnique('app_notifications_user_id_dedupe_key_unique');
            $table->unique('dedupe_key', 'app_notifications_dedupe_key_unique');
        });
    }
};
