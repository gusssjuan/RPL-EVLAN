<?php

namespace App\Services;

use App\Enums\StatusEvent;
use App\Models\ActivityLog;
use App\Models\Checklist;
use App\Models\Event;
use App\Models\Template;
use App\Models\Timeline;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EventPlanningService
{
    /**
     * Membuat acara baru dari template master dalam 1 transaksi DB.
     * (Checklist kebutuhan & timeline H-n otomatis dibuat; divisi/jobdesk tidak dibuat otomatis per D-10).
     */
    public function createFromTemplate(User $leader, array $data): Event
    {
        return DB::transaction(function () use ($leader, $data) {
            // 1. Ambil template aktif untuk kategori yang dipilih
            $template = Template::with(['kebutuhan', 'timeline', 'divisi', 'jobdesk'])
                ->where('category_id', $data['category_id'])
                ->where('is_aktif', true)
                ->first();

            // Jika tidak ada template aktif, ambil template pertama di kategori ini bila ada
            if (! $template) {
                $template = Template::with(['kebutuhan', 'timeline', 'divisi', 'jobdesk'])
                    ->where('category_id', $data['category_id'])
                    ->first();
            }

            // 2. Buat entitas Event utama
            $event = Event::create([
                'leader_id' => $leader->id,
                'category_id' => $data['category_id'],
                'nama_event' => $data['nama_event'],
                'tujuan' => $data['tujuan'],
                'tanggal' => $data['tanggal'],
                'waktu_mulai' => ! empty($data['waktu_mulai']) ? $data['waktu_mulai'] : null,
                'waktu_selesai' => ! empty($data['waktu_selesai']) ? $data['waktu_selesai'] : null,
                'lokasi' => $data['lokasi'],
                'target_peserta' => $data['target_peserta'],
                'jumlah_peserta' => (int) $data['jumlah_peserta'],
                'status_event' => StatusEvent::Perencanaan,
            ]);

            $eventDate = Carbon::parse($event->tanggal);

            // 3. Gandakan kebutuhan template ke checklists acara (status_selesai = false, dari_template = true)
            if ($template && $template->kebutuhan->isNotEmpty()) {
                foreach ($template->kebutuhan as $index => $item) {
                    Checklist::create([
                        'event_id' => $event->id,
                        'nama_kebutuhan' => $item->nama_item,
                        'kelompok' => $item->kelompok,
                        'dari_template' => true,
                        'status_selesai' => false,
                        'urutan' => $index,
                    ]);
                }
            }

            // 4. Hitung dan simpan timeline H-n acara (tanggal = tanggal_acara - offset_hari)
            $hasHariH = false;
            if ($template && $template->timeline->isNotEmpty()) {
                foreach ($template->timeline as $index => $item) {
                    $offset = (int) $item->offset_hari;
                    if ($offset === 0) {
                        $hasHariH = true;
                    }

                    $tglTimeline = $eventDate->copy()->subDays($offset);

                    Timeline::create([
                        'event_id' => $event->id,
                        'label' => $offset === 0 ? 'Hari H' : 'H-'.$offset,
                        'offset_hari' => $offset,
                        'tanggal' => $tglTimeline->toDateString(),
                        'kegiatan' => $item->nama_item,
                        'urutan' => $index,
                    ]);
                }
            }

            // D-09: Selalu pastikan ada entri "Hari H"
            if (! $hasHariH) {
                Timeline::create([
                    'event_id' => $event->id,
                    'label' => 'Hari H',
                    'offset_hari' => 0,
                    'tanggal' => $eventDate->toDateString(),
                    'kegiatan' => 'Hari Pelaksanaan Acara',
                    'urutan' => 999,
                ]);
            }

            // 5. Catat ActivityLog (audit log pembuatan acara)
            ActivityLog::create([
                'event_id' => $event->id,
                'user_id' => $leader->id,
                'aksi' => 'Buat Acara',
                'deskripsi' => 'Acara "'.$event->nama_event.'" berhasil dibuat dari template '.($template ? '"'.$template->nama_template.'"' : 'kategori').'.',
                'meta' => [
                    'category_id' => $event->category_id,
                    'template_id' => $template?->id,
                ],
            ]);

            return $event->fresh(['checklists', 'timelines', 'category', 'leader']);
        });
    }
}

