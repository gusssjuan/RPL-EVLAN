<?php

namespace App\Services;

use App\Enums\JenisNotifikasi;
use App\Models\AppNotification;
use App\Models\Event;
use App\Models\Task;
use App\Models\User;

class NotificationService
{
    /**
     * Kirim notifikasi in-app ke satu pengguna.
     *
     * Email tidak dikirim oleh service ini. Jika dedupeKey diisi, notifikasi
     * dengan kunci yang sama untuk pengguna yang sama tidak dibuat ulang.
     */
    public static function kirim(
        User $user,
        JenisNotifikasi $jenis,
        string $pesan,
        ?Event $event = null,
        ?Task $task = null,
        ?string $tautan = null,
        ?string $dedupeKey = null,
    ): AppNotification {
        $data = [
            'event_id' => $event?->id,
            'task_id' => $task?->id,
            'jenis' => $jenis,
            'pesan' => $pesan,
            'tautan' => $tautan,
            'waktu_kirim' => now(),
        ];

        if ($dedupeKey === null) {
            return AppNotification::create([
                'user_id' => $user->id,
                'dedupe_key' => null,
                ...$data,
            ]);
        }

        return AppNotification::firstOrCreate(
            [
                'user_id' => $user->id,
                'dedupe_key' => $dedupeKey,
            ],
            $data
        );
    }

    public static function jumlahBelumDibaca(User $user): int
    {
        return $user->appNotifications()->belumDibaca()->count();
    }
}
