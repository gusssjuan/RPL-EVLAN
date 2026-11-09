<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Event;
use App\Models\User;

class ActivityLogger
{
    /**
     * Catat aktivitas ke activity_logs.
     *
     * Gaya teks wireframe: "Rina Astuti mengubah status "Membuat rundown acara" → Sedang dikerjakan"
     *
     * @param  Event       $event     Acara terkait
     * @param  User        $user      Pengguna yang melakukan aksi
     * @param  string      $aksi      Jenis aksi (mis. 'buat_tugas', 'ubah_status', 'tambah_anggota', 'buat_divisi', 'hapus_tugas')
     * @param  string      $deskripsi Teks deskriptif lengkap
     * @param  array|null  $meta      Data tambahan opsional (JSON)
     */
    public static function log(Event $event, User $user, string $aksi, string $deskripsi, ?array $meta = null): ActivityLog
    {
        return ActivityLog::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'aksi' => $aksi,
            'deskripsi' => $deskripsi,
            'meta' => $meta,
        ]);
    }
}

