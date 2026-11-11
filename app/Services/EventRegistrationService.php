<?php

namespace App\Services;

use App\Enums\JenisNotifikasi;
use App\Enums\StatusEvent;
use App\Enums\StatusPendaftaran;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EventRegistrationService
{
    public function jumlahDiterima(Event $event): int
    {
        return $event->registrations()
            ->where('status', StatusPendaftaran::Diterima)
            ->count();
    }

    /**
     * Mengembalikan alasan pendaftaran tidak dapat dilakukan, atau null bila boleh.
     */
    public function alasanTidakBisaDaftar(Event $event, User $user): ?string
    {
        if ($event->isLeader($user)) {
            return 'Anda adalah ketua acara ini. Ketua tidak perlu mendaftar sebagai peserta.';
        }

        if ($event->status_event === StatusEvent::Selesai) {
            return 'Acara ini sudah selesai.';
        }

        if (! $event->is_publik) {
            return 'Acara ini tidak dipublikasikan untuk pendaftaran umum.';
        }

        if (! $event->pendaftaran_dibuka) {
            return 'Pendaftaran acara ini belum dibuka.';
        }

        if ($event->batas_pendaftaran && Carbon::today()->gt($event->batas_pendaftaran)) {
            return 'Pendaftaran sudah ditutup karena melewati batas akhir.';
        }

        if ($event->kuota !== null && $this->jumlahDiterima($event) >= $event->kuota) {
            return 'Kuota pendaftaran sudah penuh.';
        }

        if ($event->registrations()->where('user_id', $user->id)->exists()) {
            return 'Anda sudah mendaftar pada acara ini.';
        }

        return null;
    }

    public function daftar(Event $event, User $user, array $data): EventRegistration
    {
        return DB::transaction(function () use ($event, $user, $data) {
            $freshEvent = Event::query()->lockForUpdate()->findOrFail($event->id);

            $alasan = $this->alasanTidakBisaDaftar($freshEvent, $user);
            if ($alasan !== null) {
                throw ValidationException::withMessages(['pendaftaran' => $alasan]);
            }

            $registration = EventRegistration::create([
                'event_id' => $freshEvent->id,
                'user_id' => $user->id,
                'no_telepon' => $data['no_telepon'] ?? null,
                'catatan' => $data['catatan'] ?? null,
                'persetujuan_data' => true,
                'status' => StatusPendaftaran::Menunggu,
            ]);

            $leader = $freshEvent->leader()->first();
            if ($leader) {
                NotificationService::kirim(
                    $leader,
                    JenisNotifikasi::Pendaftaran,
                    $user->nama.' mendaftar ke acara "'.$freshEvent->nama_event.'".',
                    $freshEvent,
                    null,
                    route('acara.ringkasan', $freshEvent, false).'#pendaftar',
                );
            }

            return $registration;
        });
    }

    public function batalkan(EventRegistration $registration): void
    {
        if ($registration->status !== StatusPendaftaran::Menunggu) {
            throw ValidationException::withMessages([
                'pendaftaran' => 'Pendaftaran yang sudah diputuskan tidak dapat dibatalkan.',
            ]);
        }

        $registration->delete();
    }

    public function terima(EventRegistration $registration): void
    {
        DB::transaction(function () use ($registration) {
            $freshEvent = Event::query()->lockForUpdate()->findOrFail($registration->event_id);
            $fresh = EventRegistration::query()->findOrFail($registration->id);

            if ($fresh->status !== StatusPendaftaran::Menunggu) {
                throw ValidationException::withMessages([
                    'pendaftaran' => 'Pendaftaran ini sudah diputuskan sebelumnya.',
                ]);
            }

            if ($freshEvent->kuota !== null && $this->jumlahDiterima($freshEvent) >= $freshEvent->kuota) {
                throw ValidationException::withMessages([
                    'pendaftaran' => 'Kuota pendaftaran sudah penuh. Pendaftar ini belum dapat diterima.',
                ]);
            }

            $fresh->update([
                'status' => StatusPendaftaran::Diterima,
                'alasan_penolakan' => null,
                'decided_at' => now(),
            ]);

            $this->kirimKeputusan($fresh, $freshEvent, StatusPendaftaran::Diterima);
        });
    }

    public function tolak(EventRegistration $registration, string $alasan): void
    {
        DB::transaction(function () use ($registration, $alasan) {
            $freshEvent = Event::query()->findOrFail($registration->event_id);
            $fresh = EventRegistration::query()->findOrFail($registration->id);

            if ($fresh->status !== StatusPendaftaran::Menunggu) {
                throw ValidationException::withMessages([
                    'pendaftaran' => 'Pendaftaran ini sudah diputuskan sebelumnya.',
                ]);
            }

            $fresh->update([
                'status' => StatusPendaftaran::Ditolak,
                'alasan_penolakan' => $alasan,
                'decided_at' => now(),
            ]);

            $this->kirimKeputusan($fresh, $freshEvent, StatusPendaftaran::Ditolak, $alasan);
        });
    }

    private function kirimKeputusan(
        EventRegistration $registration,
        Event $event,
        StatusPendaftaran $status,
        ?string $alasan = null
    ): void {
        $peserta = $registration->user()->first();
        if (! $peserta) {
            return;
        }

        $pesan = $status === StatusPendaftaran::Diterima
            ? 'Pendaftaran Anda untuk acara "'.$event->nama_event.'" telah diterima.'
            : 'Pendaftaran Anda untuk acara "'.$event->nama_event.'" ditolak. Alasan: '.$alasan;

        NotificationService::kirim(
            $peserta,
            JenisNotifikasi::Pendaftaran,
            $pesan,
            $event,
            null,
            route('cari-acara.show', $event, false),
            'pendaftaran:'.$registration->id.':'.$status->value,
        );
    }
}
